<?php
/**
 * Página de Login - Henriquez & Asociados
 * Sistema de Gestión de Pólizas de Seguros
 * 
 * CARACTERÍSTICAS DE SEGURIDAD:
 * - Máximo 5 intentos fallidos permitidos
 * - Bloqueo temporal de 30 minutos después del 5to intento
 * - Validación de roles (Cliente/Admin)
 * - Reset automático de intentos después del bloqueo
 * - Protección contra inyección SQL
 */

// Configuración de errores para desarrollo (eliminar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Definir APP_URL si no está definida
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost/web_polizas');
}

// Incluir archivos de configuración
require_once __DIR__ . '/../../config/config.php';

// Configuración de seguridad
define('MAX_INTENTOS_FALLIDOS', 5);
define('TIEMPO_BLOQUEO_MINUTOS', 30);

// Si ya hay sesión activa, redirigir según rol
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $redirect = ($_SESSION['user_role'] === 'admin') 
        ? APP_URL . '/views/admin/dashboard.php'
        : APP_URL . '/views/cliente/dashboard.php';
    header("Location: $redirect");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";
$tiempo_bloqueo_restante = 0;

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
                id_usuario,
                nombre_completo,
                email,
                password,
                rol,
                activo
            FROM usuarios 
            WHERE email = ? AND activo = 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        
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
        
        // Registrar el acceso en logs (opcional)
        $stmt_log = $conn->prepare("
            INSERT INTO logs_acceso (id_usuario, accion, ip_address, user_agent, fecha_acceso)
            VALUES (?, 'login', ?, ?, NOW())
        ");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $stmt_log->bind_param("iss", $usuario['id_usuario'], $ip, $user_agent);
        $stmt_log->execute();
        
        // Actualizar último acceso
        $stmt_update = $conn->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?");
        $stmt_update->bind_param("i", $usuario['id_usuario']);
        $stmt_update->execute();
        
        // Redirigir según rol
        $redirect = ($usuario['rol'] === 'admin') 
            ? APP_URL . '/views/admin/dashboard.php'
            : APP_URL . '/views/cliente/dashboard.php';
        
        header("Location: $redirect");
        exit();
        
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Henriquez & Asociados</title>
    <link rel="stylesheet" href="/web_polizas/public/css/auth/login.css">
</head>
<body>
    <div class="auth-container">
        <!-- Logo y Nombre de la Empresa -->
        <div class="logo-container">
            <img src="/web_polizas/public/img/logo.png" alt="Logo Henriquez & Asociados" class="logo-utp"
                 onerror="this.style.display='none'">
        </div>

        <!-- Header del Login -->
        <div class="login-header">
            <h1>Henriquez & Asociados</h1>
            <p>Sistema de Gestión de Pólizas de Seguros</p>
        </div>

        <!-- Mensajes de Error/Éxito -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo (strpos($mensaje, 'bloqueada') !== false) ? 'alert-error blocked' : 'alert-error'; ?>">
                <?php 
                if (strpos($mensaje, 'bloqueada') !== false) {
                    echo "<strong>🔒 Cuenta Bloqueada Temporalmente</strong><br>";
                }
                echo htmlspecialchars($mensaje);
                if ($tiempo_bloqueo_restante > 0): ?>
                    <div class="countdown" id="countdown"></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Login -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    placeholder="correo@ejemplo.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    <?php echo ($tiempo_bloqueo_restante > 0) ? 'disabled' : ''; ?>
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password"
                    name="password" 
                    placeholder="Tu contraseña"
                    <?php echo ($tiempo_bloqueo_restante > 0) ? 'disabled' : ''; ?>
                    required
                >
            </div>
            
            <button 
                type="submit" 
                class="btn btn-primary"
                <?php echo ($tiempo_bloqueo_restante > 0) ? 'disabled' : ''; ?>
            >
                <?php echo ($tiempo_bloqueo_restante > 0) ? 'Cuenta Bloqueada' : 'Iniciar Sesión'; ?>
            </button>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-links">
            <a href="<?php echo APP_URL; ?>/views/auth/recuperar.php">¿Olvidaste tu contraseña?</a>
            <a href="<?php echo APP_URL; ?>/views/auth/registro.php">Crear una cuenta</a>
        </div>
    </div>

    <?php if ($tiempo_bloqueo_restante > 0): ?>
    <script>
        let segundosRestantes = <?php echo $tiempo_bloqueo_restante * 60; ?>;
        
        function actualizarContador() {
            const minutos = Math.floor(segundosRestantes / 60);
            const segundos = segundosRestantes % 60;
            
            document.getElementById('countdown').innerHTML = 
                `Tiempo restante: ${minutos}:${segundos.toString().padStart(2, '0')}`;
            
            if (segundosRestantes > 0) {
                segundosRestantes--;
                setTimeout(actualizarContador, 1000);
            } else {
                location.reload();
            }
        }
        
        actualizarContador();
    </script>
    <?php endif; ?>
</body>
</html>