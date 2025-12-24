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
    
    // Si hay una fecha de bloqueo y aún no ha expirado
    if ($resultado['cuenta_bloqueada_hasta'] && strtotime($resultado['cuenta_bloqueada_hasta']) > time()) {
        return [
            'bloqueada' => true, 
            'minutos_restantes' => $resultado['minutos_restantes'],
            'intentos' => $resultado['intentos_fallidos']
        ];
    } 
    
    // Si el bloqueo expiró, resetear contador
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
    // Obtener intentos actuales
    $stmt = $conn->prepare("SELECT intentos_fallidos FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$resultado) {
        return ['bloqueada' => false, 'intentos' => 0];
    }
    
    $nuevos_intentos = $resultado['intentos_fallidos'] + 1;
    
    // Si alcanzó el máximo de intentos, bloquear cuenta
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
        // Solo incrementar contador
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

// Procesar formulario de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener conexión a la base de datos
        $conn = getDBConnection();
        
        // Validar y limpiar datos
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
        
        // Cerrar conexión
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
        
        // Cerrar conexión si está abierta
        if (isset($conn) && $conn->ping()) {
            $conn->close();
        }
    }
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
            height: 550px;
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

        .container.register-mode .overlay-right {
            opacity: 1;
            transition: 0.6s ease-in-out 0.4s;
        }

        .overlay-right {
            position: absolute;
            right: 0;
            opacity: 0;
            transition: 0.6s ease-in-out;
        }

        .container.register-mode .overlay-right {
            opacity: 1;
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
            padding: 40px;
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
            font-size: 32px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 50px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .input-group input:focus {
            outline: none;
            border-color: #004B93;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 75, 147, 0.1);
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #004B93;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #003366;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #004B93;
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
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

        .social-login {
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            color: #999;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .social-icons a {
            width: 42px;
            height: 42px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .social-icons a:hover {
            border-color: #004B93;
            background: #004B93;
            color: white;
            transform: translateY(-3px);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
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
                display: none;
            }

            .form-container.login-container .mobile-switch,
            .form-container.register-container .mobile-switch {
                display: block;
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
            }
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <!-- Formulario de Login -->
        <div class="form-container login-container">
            <div class="form-box">
                <h3>Login</h3>
                
                <div id="alertContainer"></div>

                <form method="POST" action="">
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="/Gestion_de_Polizas/views/auth/recuperar.php">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="submit-btn">Login</button>
                </form>

                <div class="social-login">
                    <p>or login with social platforms</p>
                    <div class="social-icons">
                        <a href="#" title="Google">G</a>
                        <a href="#" title="Facebook">f</a>
                        <a href="#" title="GitHub">⚡</a>
                        <a href="#" title="LinkedIn">in</a>
                    </div>
                </div>

                <div class="mobile-switch" style="display: none;">
                    <p>Don't have an account?</p>
                    <a href="/Gestion_de_Polizas/views/auth/registro.php">Register here</a>
                </div>
            </div>
        </div>

        <!-- Formulario de Registro -->
        <div class="form-container register-container">
            <div class="form-box">
                <h3>Registration</h3>
                
                <div id="registerAlertContainer"></div>

                <form method="POST" action="">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Register</button>
                </form>

                <div class="social-login">
                    <p>or register with social platforms</p>
                    <div class="social-icons">
                        <a href="#" title="Google">G</a>
                        <a href="#" title="Facebook">f</a>
                        <a href="#" title="GitHub">⚡</a>
                        <a href="#" title="LinkedIn">in</a>
                    </div>
                </div>

                <div class="mobile-switch" style="display: none;">
                    <p>Already have an account?</p>
                    <a href="/Gestion_de_Polizas/views/auth/login.php">Login here</a>
                </div>
            </div>
        </div>

        <!-- Panel deslizante -->
        <div class="overlay-panel">
            <div class="overlay-content overlay-left">
                <h2>Hello, Welcome!</h2>
                <p>Don't have an Account?</p>
                <button onclick="showRegister()">Register</button>
            </div>
            <div class="overlay-content overlay-right">
                <h2>Welcome Back!</h2>
                <p>Already have an Account?</p>
                <button onclick="showLogin()">Login</button>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');

        function showRegister() {
            container.classList.add('register-mode');
        }

        function showLogin() {
            container.classList.remove('register-mode');
        }

        // Detectar si venimos de un enlace específico
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('mode') === 'register') {
            showRegister();
        }
    </script>
</body>
</html>