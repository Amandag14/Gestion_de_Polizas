<?php
// Configuración de errores para desarrollo (eliminar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Definir APP_URL si no está definida
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/Gestion_de_Polizas');
}

// Incluir archivos de configuración
require_once __DIR__ . '/../../config/config.php';

// Configuración de seguridad
define('MAX_INTENTOS_FALLIDOS', 5);
define('TIEMPO_BLOQUEO_MINUTOS', 30);

// Si ya hay sesión activa, redirigir según rol
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $redirect = ($_SESSION['user_role'] === 'admin') 
        ? APP_URL . '/views/admin/dashboardAdmin.php'
        : APP_URL . '/views/cliente/dashboardCliente.php';
    header("Location: $redirect");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";
$tiempo_bloqueo_restante = 0;
$mostrar_registro = false;

/**
 * Obtener conexión a la base de datos (MySQLi)
 */
function getDBConnection() {
    $conn = new mysqli('localhost', 'root', '', 'henriquez_seguros');
    
    if ($conn->connect_error) {
        error_log("Error de conexión: " . $conn->connect_error);
        throw new Exception("Error de conexión a la base de datos");
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Verificar si una cuenta está bloqueada
 */
function verificarBloqueo($conn, $email) {
    $stmt = $conn->prepare("
        SELECT 
            intentos_fallidos,
            cuenta_bloqueada_hasta,
            TIMESTAMPDIFF(MINUTE, NOW(), cuenta_bloqueada_hasta) as minutos_restantes
        FROM usuarios 
        WHERE email = ? AND activo = 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$resultado) {
        return ['bloqueada' => false, 'minutos_restantes' => 0, 'intentos' => 0];
    }
    
    if ($resultado['cuenta_bloqueada_hasta'] && strtotime($resultado['cuenta_bloqueada_hasta']) > time()) {
        return [
            'bloqueada' => true, 
            'minutos_restantes' => $resultado['minutos_restantes'],
            'intentos' => $resultado['intentos_fallidos']
        ];
    } 
    
    if ($resultado['cuenta_bloqueada_hasta'] && strtotime($resultado['cuenta_bloqueada_hasta']) <= time()) {
        $stmt_reset = $conn->prepare("
            UPDATE usuarios 
            SET intentos_fallidos = 0,
                cuenta_bloqueada_hasta = NULL,
                fecha_ultimo_intento_fallido = NULL
            WHERE email = ?
        ");
        $stmt_reset->bind_param("s", $email);
        $stmt_reset->execute();
        $stmt_reset->close();
        return ['bloqueada' => false, 'minutos_restantes' => 0, 'intentos' => 0];
    }
    
    return [
        'bloqueada' => false, 
        'minutos_restantes' => 0, 
        'intentos' => $resultado['intentos_fallidos']
    ];
}

/**
 * Registrar intento fallido
 */
function registrarIntentoFallido($conn, $email) {
    $stmt = $conn->prepare("SELECT intentos_fallidos FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$resultado) {
        return ['bloqueada' => false, 'intentos' => 0];
    }
    
    $nuevos_intentos = $resultado['intentos_fallidos'] + 1;
    
    if ($nuevos_intentos >= MAX_INTENTOS_FALLIDOS) {
        $fecha_desbloqueo = date('Y-m-d H:i:s', strtotime('+' . TIEMPO_BLOQUEO_MINUTOS . ' minutes'));
        
        $stmt_update = $conn->prepare("
            UPDATE usuarios 
            SET intentos_fallidos = ?,
                fecha_ultimo_intento_fallido = NOW(),
                cuenta_bloqueada_hasta = ?
            WHERE email = ?
        ");
        $stmt_update->bind_param("iss", $nuevos_intentos, $fecha_desbloqueo, $email);
        $stmt_update->execute();
        $stmt_update->close();
        
        return ['bloqueada' => true, 'intentos' => $nuevos_intentos];
    } else {
        $stmt_update = $conn->prepare("
            UPDATE usuarios 
            SET intentos_fallidos = ?,
                fecha_ultimo_intento_fallido = NOW()
            WHERE email = ?
        ");
        $stmt_update->bind_param("is", $nuevos_intentos, $email);
        $stmt_update->execute();
        $stmt_update->close();
        
        return ['bloqueada' => false, 'intentos' => $nuevos_intentos];
    }
}

/**
 * Resetear intentos fallidos (después de login exitoso)
 */
function resetearIntentos($conn, $email) {
    $stmt = $conn->prepare("
        UPDATE usuarios 
        SET intentos_fallidos = 0,
            cuenta_bloqueada_hasta = NULL,
            fecha_ultimo_intento_fallido = NULL
        WHERE email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
}

/**
 * Validar email
 */
function validarEmail($email) {
    $email = trim($email);
    if (empty($email)) {
        throw new Exception("El email es obligatorio.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El formato del email no es válido.");
    }
    return strtolower($email);
}

/**
 * Validar contraseña
 */
function validarPassword($password) {
    if (empty($password)) {
        throw new Exception("La contraseña es obligatoria.");
    }
    if (strlen($password) < 6) {
        throw new Exception("La contraseña debe tener al menos 6 caracteres.");
    }
    return $password;
}

/**
 * Validar cédula panameña
 */
function validarCedula($cedula) {
    if (empty($cedula)) {
        return null;
    }
    
    $patron = '/^[0-9]{1,2}-[0-9]{3,4}-[0-9]{4}$/';
    if (!preg_match($patron, $cedula)) {
        throw new Exception("Formato de cédula inválido. Use: X-XXX-XXXX");
    }
    
    return $cedula;
}

// ============================================
// PROCESAR FORMULARIOS
// ============================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Detectar qué formulario se envió
    $form_type = $_POST['form_type'] ?? '';
    
    // ========== PROCESAR LOGIN ==========
    if ($form_type === 'login') {
        try {
            $conn = getDBConnection();
            
            $email = validarEmail($_POST["email"] ?? '');
            $password = validarPassword($_POST["password"] ?? '');
            
            // Verificar bloqueo de cuenta
            $estado_bloqueo = verificarBloqueo($conn, $email);
            
            if ($estado_bloqueo['bloqueada']) {
                $tiempo_bloqueo_restante = $estado_bloqueo['minutos_restantes'];
                throw new Exception("Cuenta bloqueada temporalmente por seguridad. Intente nuevamente en " . $tiempo_bloqueo_restante . " minutos.");
            }
            
            // Buscar usuario en la base de datos
            $stmt = $conn->prepare("
                SELECT 
                    id AS id_usuario,
                    nombre_completo,
                    email,
                    password_hash AS password,
                    'admin' AS rol,
                    activo
                FROM usuarios 
                WHERE email = ? AND activo = 1
            ");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $usuario = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            // Si no se encuentra en usuarios, buscar en clientes
            if (!$usuario) {
                $stmt = $conn->prepare("
                    SELECT 
                        id AS id_usuario,
                        CONCAT(nombre, ' ', apellido) AS nombre_completo,
                        email,
                        password_hash AS password,
                        'cliente' AS rol,
                        1 AS activo
                    FROM clientes 
                    WHERE email = ? AND aprobado = 1 AND estado_cuenta = 'Activo'
                ");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $usuario = $stmt->get_result()->fetch_assoc();
                $stmt->close();
            }
            
            // Verificar si el usuario existe
            if (!$usuario) {
                $resultado_intento = registrarIntentoFallido($conn, $email);
                $intentos_restantes = MAX_INTENTOS_FALLIDOS - $resultado_intento['intentos'];
                
                if ($resultado_intento['bloqueada']) {
                    throw new Exception("Cuenta bloqueada temporalmente por seguridad. Intente nuevamente en " . TIEMPO_BLOQUEO_MINUTOS . " minutos.");
                } else {
                    throw new Exception("Credenciales inválidas. Le quedan " . $intentos_restantes . " intentos antes del bloqueo temporal.");
                }
            }
            
            // Verificar contraseña
            $password_valida = password_verify($password, $usuario['password']);
            
            if (!$password_valida) {
                $resultado_intento = registrarIntentoFallido($conn, $email);
                $intentos_restantes = MAX_INTENTOS_FALLIDOS - $resultado_intento['intentos'];
                
                if ($resultado_intento['bloqueada']) {
                    throw new Exception("Cuenta bloqueada temporalmente por seguridad. Intente nuevamente en " . TIEMPO_BLOQUEO_MINUTOS . " minutos.");
                } else {
                    throw new Exception("Contraseña incorrecta. Le quedan " . $intentos_restantes . " intentos antes del bloqueo temporal.");
                }
            }
            
            // Autenticación exitosa
            resetearIntentos($conn, $email);
            
            // Crear sesión
            $_SESSION['user_id'] = $usuario['id_usuario'];
            $_SESSION['user_name'] = $usuario['nombre_completo'];
            $_SESSION['user_email'] = $usuario['email'];
            $_SESSION['user_role'] = $usuario['rol'];
            $_SESSION['last_activity'] = time();
            
            // Registrar el acceso en logs (si existe la tabla)
            $stmt_check = $conn->query("SHOW TABLES LIKE 'logs_actividad'");
            if ($stmt_check->num_rows > 0) {
                $stmt_log = $conn->prepare("
                    INSERT INTO logs_actividad 
                    (usuario_id, tipo_usuario, accion, ip_address, user_agent, created_at)
                    VALUES (?, ?, 'login', ?, ?, NOW())
                ");
                $tipo_usuario = ($usuario['rol'] === 'admin') ? 'Usuario' : 'Cliente';
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                $stmt_log->bind_param("isss", $usuario['id_usuario'], $tipo_usuario, $ip, $user_agent);
                $stmt_log->execute();
                $stmt_log->close();
            }
            
            // Actualizar último acceso
            $tabla = ($usuario['rol'] === 'admin') ? 'usuarios' : 'clientes';
            $stmt_update = $conn->prepare("UPDATE $tabla SET ultimo_acceso = NOW() WHERE id = ?");
            $stmt_update->bind_param("i", $usuario['id_usuario']);
            $stmt_update->execute();
            $stmt_update->close();
            
            $conn->close();
            
            // Redirigir según rol
            $redirect = ($usuario['rol'] === 'admin') 
                ? APP_URL . '/views/admin/dashboardAdmin.php'
                : APP_URL . '/views/cliente/dashboardCliente.php';
            
            header("Location: $redirect");
            exit();
            
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = "error";
            
            if (isset($conn) && $conn->ping()) {
                $conn->close();
            }
        }
    }
    
    // ========== PROCESAR REGISTRO ==========
    else if ($form_type === 'register') {
        try {
            $conn = getDBConnection();
            
            // Obtener y validar datos del formulario
            $tipo_cliente = $_POST['tipo_cliente'] ?? '';
            $email = validarEmail($_POST['reg_email'] ?? '');
            $password = validarPassword($_POST['reg_password'] ?? '');
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validar que las contraseñas coincidan
            if ($password !== $confirm_password) {
                throw new Exception("Las contraseñas no coinciden.");
            }
            
            // Verificar que el email no exista
            $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->fetch_assoc()) {
                throw new Exception("Este email ya está registrado.");
            }
            $stmt->close();
            
            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Preparar datos según tipo de cliente
            if ($tipo_cliente === 'Personal') {
                $nombre = trim($_POST['nombre'] ?? '');
                $apellido = trim($_POST['apellido'] ?? '');
                $cedula = validarCedula($_POST['cedula'] ?? '');
                $razon_social = null;
                $ruc = null;
                
                if (empty($nombre)) {
                    throw new Exception("El nombre es obligatorio.");
                }
                if (empty($apellido)) {
                    throw new Exception("El apellido es obligatorio.");
                }
                
            } else if ($tipo_cliente === 'Empresa') {
                $nombre = null;
                $apellido = null;
                $cedula = null;
                $razon_social = trim($_POST['razon_social'] ?? '');
                $ruc = trim($_POST['ruc'] ?? '') ?: null;
                
                if (empty($razon_social)) {
                    throw new Exception("La razón social es obligatoria.");
                }
            } else {
                throw new Exception("Tipo de cliente inválido.");
            }
            
            // Otros datos
            $telefono = trim($_POST['telefono'] ?? '') ?: null;
            $celular = trim($_POST['celular'] ?? '');
            $provincia = $_POST['provincia'] ?? null;
            $direccion = trim($_POST['direccion'] ?? '') ?: null;
            
            if (empty($celular)) {
                throw new Exception("El celular es obligatorio.");
            }
            
            // Insertar en la base de datos
            $aprobado = 1; // 1 = auto-aprobación
            $estado_cuenta = 'Activo';
            
            $stmt = $conn->prepare("
                INSERT INTO clientes (
                    email, 
                    password_hash, 
                    cedula, 
                    ruc,
                    nombre, 
                    apellido, 
                    razon_social,
                    tipo_cliente,
                    telefono, 
                    celular,
                    direccion,
                    provincia,
                    aprobado,
                    estado_cuenta
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param(
                "ssssssssssssis",
                $email,
                $password_hash,
                $cedula,
                $ruc,
                $nombre,
                $apellido,
                $razon_social,
                $tipo_cliente,
                $telefono,
                $celular,
                $direccion,
                $provincia,
                $aprobado,
                $estado_cuenta
            );
            
            if ($stmt->execute()) {
                $mensaje = "¡Registro exitoso! Puedes iniciar sesión ahora.";
                $tipo_mensaje = "success";
                $mostrar_registro = false; // Volver a mostrar login
                
                $cliente_id = $stmt->insert_id;
                error_log("Nuevo cliente registrado: ID $cliente_id, Email: $email");
                
            } else {
                throw new Exception("Error al registrar el usuario.");
            }
            
            $stmt->close();
            $conn->close();
            
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = "error";
            $mostrar_registro = true; // Mantener en vista de registro
            
            if (isset($conn) && $conn->ping()) {
                $conn->close();
            }
        }
    }
}

// Detectar si venimos de un enlace de registro
if (isset($_GET['mode']) && $_GET['mode'] === 'register') {
    $mostrar_registro = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Henriquez & Asociados</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 20px;
        }

        .container {
            position: relative;
            width: 900px;
            height: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        /* Panel deslizante de bienvenida */
        .overlay-panel {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: #004B93;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            z-index: 100;
            transition: 0.6s ease-in-out;
            transform: translateX(0);
        }

        .container.register-mode .overlay-panel {
            transform: translateX(100%);
        }

        .overlay-content {
            text-align: center;
            transition: 0.6s ease-in-out 0.2s;
            opacity: 1;
        }

        .container.register-mode .overlay-left {
            opacity: 0;
            transition: 0.6s ease-in-out;
        }

        .overlay-right {
            position: absolute;
            right: 0;
            opacity: 0;
            transition: 0.6s ease-in-out;
        }

        .container.register-mode .overlay-right {
            opacity: 1;
            transition: 0.6s ease-in-out 0.4s;
        }

        .overlay-panel h2 {
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .overlay-panel p {
            font-size: 16px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .overlay-panel button {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 45px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .overlay-panel button:hover {
            background: white;
            color: #004B93;
            transform: scale(1.05);
        }

        /* Contenedor de formularios */
        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: 0.6s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            overflow-y: auto;
        }

        .form-container.login-container {
            right: 0;
            z-index: 2;
            opacity: 1;
        }

        .form-container.register-container {
            left: 0;
            z-index: 1;
            opacity: 0;
            pointer-events: none;
        }

        .container.register-mode .login-container {
            opacity: 0;
            z-index: 1;
            pointer-events: none;
            transition: 0.6s ease-in-out;
        }

        .container.register-mode .register-container {
            opacity: 1;
            z-index: 2;
            pointer-events: all;
            transition: 0.6s ease-in-out 0.4s;
        }

        .form-box {
            width: 100%;
            max-width: 380px;
        }

        .form-box h3 {
            color: #333;
            font-size: 28px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
        }

        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: #004B93;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 75, 147, 0.1);
        }

        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #004B93;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #003366;
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: #004B93;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 75, 147, 0.4);
            background: #003366;
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background-color: #fee;
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background-color: #d4edda;
            color: #28a745;
            border-left: 4px solid #28a745;
        }

        .campos-dinamicos {
            display: none;
        }

        .campos-dinamicos.active {
            display: block;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 13px;
            font-weight: 500;
        }

        .form-section {
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .container {
                width: 100%;
                max-width: 450px;
                height: auto;
                min-height: 600px;
            }

            .overlay-panel {
                display: none;
            }

            .form-container {
                width: 100%;
                position: relative;
            }

            .form-container.login-container,
            .form-container.register-container {
                opacity: 1;
                pointer-events: all;
            }

            .mobile-switch {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e8e8e8;
            }

            .mobile-switch p {
                color: #666;
                font-size: 14px;
                margin-bottom: 10px;
            }

            .mobile-switch a {
                color: #004B93;
                text-decoration: none;
                font-weight: 600;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="container" <?php echo $mostrar_registro ? 'class="register-mode"' : ''; ?>>
        
        <!-- ============================================ -->
        <!-- FORMULARIO DE LOGIN -->
        <!-- ============================================ -->
        <div class="form-container login-container">
            <div class="form-box">
                <h3>Iniciar Sesión</h3>
                
                <?php if (!$mostrar_registro && !empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="<?php echo APP_URL; ?>/views/auth/recuperar.php">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="submit-btn">Ingresar</button>
                </form>

                <div class="mobile-switch">
                    <p>¿No tienes cuenta?</p>
                    <a onclick="showRegister()">Regístrate aquí</a>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FORMULARIO DE REGISTRO -->
        <!-- ============================================ -->
        <div class="form-container register-container">
            <div class="form-box">
                <h3>Crear Cuenta</h3>
                
                <?php if ($mostrar_registro && !empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="registerForm">
                    <input type="hidden" name="form_type" value="register">
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label>Tipo de Cliente *</label>
                            <select name="tipo_cliente" id="tipo_cliente" required onchange="toggleCampos()">
                                <option value="">Seleccione...</option>
                                <option value="Personal">Personal</option>
                                <option value="Empresa">Empresa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campos para Personal -->
                    <div id="campos-personal" class="campos-dinamicos">
                        <div class="input-row">
                            <div class="input-group">
                                <input type="text" name="nombre" placeholder="Nombre" id="nombre">
                            </div>
                            <div class="input-group">
                                <input type="text" name="apellido" placeholder="Apellido" id="apellido">
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" name="cedula" placeholder="Cédula (X-XXX-XXXX)" id="cedula">
                        </div>
                    </div>

                    <!-- Campos para Empresa -->
                    <div id="campos-empresa" class="campos-dinamicos">
                        <div class="input-group">
                            <input type="text" name="razon_social" placeholder="Razón Social" id="razon_social">
                        </div>
                        <div class="input-group">
                            <input type="text" name="ruc" placeholder="RUC (Opcional)" id="ruc">
                        </div>
                    </div>

                    <!-- Campos comunes -->
                    <div class="form-section">
                        <div class="input-group">
                            <input type="email" name="reg_email" placeholder="Email *" required>
                        </div>
                        
                        <div class="input-row">
                            <div class="input-group">
                                <input type="tel" name="telefono" placeholder="Teléfono">
                            </div>
                            <div class="input-group">
                                <input type="tel" name="celular" placeholder="Celular *" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <select name="provincia">
                                <option value="">Provincia</option>
                                <option value="Panamá">Panamá</option>
                                <option value="Colón">Colón</option>
                                <option value="Chiriquí">Chiriquí</option>
                                <option value="Bocas del Toro">Bocas del Toro</option>
                                <option value="Veraguas">Veraguas</option>
                                <option value="Herrera">Herrera</option>
                                <option value="Los Santos">Los Santos</option>
                                <option value="Coclé">Coclé</option>
                                <option value="Darién">Darién</option>
                                <option value="Panamá Oeste">Panamá Oeste</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <input type="text" name="direccion" placeholder="Dirección">
                        </div>

                        <div class="input-group">
                            <input type="password" name="reg_password" placeholder="Contraseña *" required minlength="6">
                        </div>
                        
                        <div class="input-group">
                            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña *" required minlength="6">
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarme</button>
                </form>

                <div class="mobile-switch">
                    <p>¿Ya tienes cuenta?</p>
                    <a onclick="showLogin()">Inicia sesión aquí</a>
                </div>
            </div>
        </div>

        <!-- Panel deslizante -->
        <div class="overlay-panel">
            <div class="overlay-content overlay-left">
                <h2>¡Bienvenido!</h2>
                <p>¿No tienes cuenta?</p>
                <button onclick="showRegister()">Regístrate</button>
            </div>
            <div class="overlay-content overlay-right">
                <h2>¡Bienvenido de nuevo!</h2>
                <p>¿Ya tienes cuenta?</p>
                <button onclick="showLogin()">Inicia Sesión</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');

        function showRegister() {
            container.classList.add('register-mode');
            // Actualizar URL sin recargar
            window.history.pushState({}, '', '?mode=register');
        }

        function showLogin() {
            container.classList.remove('register-mode');
            // Actualizar URL sin recargar
            window.history.pushState({}, '', window.location.pathname);
        }

        // Mostrar/ocultar campos según tipo de cliente
        function toggleCampos() {
            const tipoCliente = document.getElementById('tipo_cliente').value;
            const camposPersonal = document.getElementById('campos-personal');
            const camposEmpresa = document.getElementById('campos-empresa');
            
            // Ocultar todos
            camposPersonal.classList.remove('active');
            camposEmpresa.classList.remove('active');
            
            // Deshabilitar todos los campos
            document.getElementById('nombre').required = false;
            document.getElementById('apellido').required = false;
            document.getElementById('cedula').required = false;
            document.getElementById('razon_social').required = false;
            
            if (tipoCliente === 'Personal') {
                camposPersonal.classList.add('active');
                document.getElementById('nombre').required = true;
                document.getElementById('apellido').required = true;
            } else if (tipoCliente === 'Empresa') {
                camposEmpresa.classList.add('active');
                document.getElementById('razon_social').required = true;
            }
        }

        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Si hay un mensaje de éxito en registro, mostrar login después de 3 segundos
            const alertSuccess = document.querySelector('.alert-success');
            if (alertSuccess && container.classList.contains('register-mode')) {
                setTimeout(function() {
                    showLogin();
                }, 3000);
            }
        });
    </script>
</body>
</html>