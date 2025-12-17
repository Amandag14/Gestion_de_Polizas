<?php
// Configuración de errores para desarrollo
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

// Importar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$mensaje = "";
$tipo_mensaje = "";
$paso = 1; // 1: Solicitar email, 2: Ingresar código, 3: Nueva contraseña

/**
 * Generar código de verificación de 6 dígitos
 */
function generarCodigoVerificacion() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Enviar email con código de verificación
 */
function enviarCodigoEmail($email, $codigo, $nombre) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Cambiar según tu proveedor
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu-email@gmail.com'; // Tu email
        $mail->Password   = 'tu-contraseña-app'; // Contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Configuración del correo
        $mail->setFrom('noreply@henriquez.com', 'Henriquez & Asociados');
        $mail->addAddress($email, $nombre);
        
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Código de Recuperación de Contraseña';
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #004B93, #0066B3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .code-box { background: white; border: 2px dashed #004B93; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px; }
                .code { font-size: 32px; font-weight: bold; color: #004B93; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Recuperación de Contraseña</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
                    
                    <div class='code-box'>
                        <p style='margin: 0 0 10px 0;'>Tu código de verificación es:</p>
                        <div class='code'>{$codigo}</div>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong>
                        <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                            <li>Este código expira en <strong>15 minutos</strong></li>
                            <li>Si no solicitaste este cambio, ignora este correo</li>
                            <li>Nunca compartas este código con nadie</li>
                        </ul>
                    </div>
                    
                    <p>Si tienes alguna duda, contáctanos.</p>
                    
                    <p style='margin-top: 30px;'>
                        Saludos,<br>
                        <strong>Equipo de Henriquez & Asociados</strong>
                    </p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no responder.</p>
                    <p>&copy; " . date('Y') . " Henriquez & Asociados. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Tu código de verificación es: {$codigo}\n\nEste código expira en 15 minutos.\nSi no solicitaste este cambio, ignora este correo.";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar email: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Guardar código en la base de datos
 */
function guardarCodigoVerificacion($conn, $email, $codigo) {
    // Eliminar códigos anteriores del usuario
    $stmt = $conn->prepare("DELETE FROM codigos_verificacion WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Insertar nuevo código (expira en 15 minutos)
    $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $stmt = $conn->prepare("
        INSERT INTO codigos_verificacion (email, codigo, expiracion, usado)
        VALUES (?, ?, ?, 0)
    ");
    $stmt->bind_param("sss", $email, $codigo, $expiracion);
    return $stmt->execute();
}

/**
 * Verificar código
 */
function verificarCodigo($conn, $email, $codigo) {
    $stmt = $conn->prepare("
        SELECT id, expiracion 
        FROM codigos_verificacion 
        WHERE email = ? AND codigo = ? AND usado = 0
    ");
    $stmt->bind_param("ss", $email, $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    
    if (!$resultado) {
        return ['valido' => false, 'mensaje' => 'Código incorrecto o ya utilizado'];
    }
    
    if (strtotime($resultado['expiracion']) < time()) {
        return ['valido' => false, 'mensaje' => 'El código ha expirado. Solicita uno nuevo'];
    }
    
    return ['valido' => true, 'id' => $resultado['id']];
}

/**
 * Marcar código como usado
 */
function marcarCodigoUsado($conn, $codigo_id) {
    $stmt = $conn->prepare("UPDATE codigos_verificacion SET usado = 1 WHERE id = ?");
    $stmt->bind_param("i", $codigo_id);
    return $stmt->execute();
}

// Procesar formularios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = getDBConnection();
        
        // PASO 1: Enviar código al email
        if (isset($_POST['accion']) && $_POST['accion'] === 'enviar_codigo') {
            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            
            if (!$email) {
                throw new Exception("Por favor ingresa un correo electrónico válido.");
            }
            
            // Verificar si el email existe
            $stmt = $conn->prepare("SELECT id_usuario, nombre_completo, email FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $usuario = $stmt->get_result()->fetch_assoc();
            
            if (!$usuario) {
                throw new Exception("No existe una cuenta asociada a este correo electrónico.");
            }
            
            // Generar y guardar código
            $codigo = generarCodigoVerificacion();
            
            if (!guardarCodigoVerificacion($conn, $email, $codigo)) {
                throw new Exception("Error al generar el código. Intenta nuevamente.");
            }
            
            // Enviar email
            if (enviarCodigoEmail($email, $codigo, $usuario['nombre_completo'])) {
                $_SESSION['recuperar_email'] = $email;
                $paso = 2;
                $tipo_mensaje = "success";
                $mensaje = "Código enviado exitosamente. Revisa tu correo electrónico.";
            } else {
                throw new Exception("Error al enviar el correo. Verifica tu conexión e intenta nuevamente.");
            }
        }
        
        // PASO 2: Verificar código
        elseif (isset($_POST['accion']) && $_POST['accion'] === 'verificar_codigo') {
            if (!isset($_SESSION['recuperar_email'])) {
                throw new Exception("Sesión expirada. Inicia el proceso nuevamente.");
            }
            
            $codigo = trim($_POST['codigo']);
            $email = $_SESSION['recuperar_email'];
            
            if (strlen($codigo) !== 6) {
                throw new Exception("El código debe tener 6 dígitos.");
            }
            
            $verificacion = verificarCodigo($conn, $email, $codigo);
            
            if (!$verificacion['valido']) {
                throw new Exception($verificacion['mensaje']);
            }
            
            $_SESSION['codigo_verificado'] = true;
            $_SESSION['codigo_id'] = $verificacion['id'];
            $paso = 3;
            $tipo_mensaje = "success";
            $mensaje = "Código verificado. Ahora puedes establecer tu nueva contraseña.";
        }
        
        // PASO 3: Cambiar contraseña
        elseif (isset($_POST['accion']) && $_POST['accion'] === 'cambiar_password') {
            if (!isset($_SESSION['recuperar_email']) || !isset($_SESSION['codigo_verificado'])) {
                throw new Exception("Sesión expirada. Inicia el proceso nuevamente.");
            }
            
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $email = $_SESSION['recuperar_email'];
            
            // Validar contraseñas
            if (strlen($password) < 8) {
                throw new Exception("La contraseña debe tener al menos 8 caracteres.");
            }
            
            if ($password !== $confirm_password) {
                throw new Exception("Las contraseñas no coinciden.");
            }
            
            // Actualizar contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $password_hash, $email);
            
            if ($stmt->execute()) {
                // Marcar código como usado
                marcarCodigoUsado($conn, $_SESSION['codigo_id']);
                
                // Limpiar sesión
                unset($_SESSION['recuperar_email']);
                unset($_SESSION['codigo_verificado']);
                unset($_SESSION['codigo_id']);
                
                $paso = 4; // Vista de éxito
                $tipo_mensaje = "success";
                $mensaje = "¡Contraseña actualizada exitosamente!";
            } else {
                throw new Exception("Error al actualizar la contraseña. Intenta nuevamente.");
            }
        }
        
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = "error";
    }
} else {
    // Determinar en qué paso estamos basado en la sesión
    if (isset($_SESSION['codigo_verificado']) && $_SESSION['codigo_verificado']) {
        $paso = 3;
    } elseif (isset($_SESSION['recuperar_email'])) {
        $paso = 2;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Henriquez & Asociados</title>
    <link rel="stylesheet" href="/Gestion_de_Polizas/public/css/auth/login.css">
</head>
<body>
    <div class="auth-container">
        <!-- Logo y Nombre de la Empresa -->
        <div class="logo-container">
            <img src="/Gestion_de_Polizas/public/img/HaLogo-1.png" alt="Logo Henriquez & Asociados" class="logo-ha"
                 onerror="this.style.display='none'">
        </div>

        <!-- Header -->
        <div class="login-header">
            <h1>
                <?php 
                    if ($paso === 1) echo "Recuperar Contraseña";
                    elseif ($paso === 2) echo "Verificar Código";
                    elseif ($paso === 3) echo "Nueva Contraseña";
                    elseif ($paso === 4) echo "¡Listo!";
                ?>
            </h1>
            <p>
                <?php 
                    if ($paso === 1) echo "Ingresa tu correo para recibir el código de verificación";
                    elseif ($paso === 2) echo "Ingresa el código que enviamos a tu correo";
                    elseif ($paso === 3) echo "Establece tu nueva contraseña segura";
                    elseif ($paso === 4) echo "Tu contraseña ha sido actualizada";
                ?>
            </p>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if ($paso === 1): ?>
            <!-- PASO 1: Solicitar Email -->
            <form method="POST" action="">
                <input type="hidden" name="accion" value="enviar_codigo">
                
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Enviar Código de Verificación
                </button>
            </form>

        <?php elseif ($paso === 2): ?>
            <!-- PASO 2: Verificar Código -->
            <form method="POST" action="">
                <input type="hidden" name="accion" value="verificar_codigo">
                
                <div class="form-group">
                    <label for="codigo">Código de Verificación</label>
                    <input 
                        type="text" 
                        id="codigo"
                        name="codigo" 
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                        style="text-align: center; font-size: 24px; letter-spacing: 5px;"
                    >
                    <small style="display: block; margin-top: 8px; color: #666;">
                        Revisa tu correo: <?php echo htmlspecialchars($_SESSION['recuperar_email']); ?>
                    </small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Verificar Código
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="?reintentar=1" style="color: #0066B3; font-size: 14px; text-decoration: none;">
                    ¿No recibiste el código? Reenviar
                </a>
            </div>

        <?php elseif ($paso === 3): ?>
            <!-- PASO 3: Nueva Contraseña -->
            <form method="POST" action="" id="passwordForm">
                <input type="hidden" name="accion" value="cambiar_password">
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="Mínimo 8 caracteres"
                        required
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="confirm_password"
                        name="confirm_password" 
                        placeholder="Repite tu contraseña"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Cambiar Contraseña
                </button>
            </form>
            
            <script>
                document.getElementById('passwordForm').addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirm = document.getElementById('confirm_password').value;
                    
                    if (password !== confirm) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden');
                    }
                });
            </script>

        <?php elseif ($paso === 4): ?>
            <!-- PASO 4: Éxito -->
            <div style="text-align: center; padding: 20px 0;">
                <div style="font-size: 64px; color: #28a745; margin-bottom: 20px;">✓</div>
                <p style="color: #666; margin-bottom: 30px;">
                    Tu contraseña ha sido actualizada exitosamente.<br>
                    Ya puedes iniciar sesión con tu nueva contraseña.
                </p>
                <a href="<?php echo APP_URL; ?>/views/auth/login.php" class="btn btn-primary">
                    Ir al Login
                </a>
            </div>
        <?php endif; ?>

        <?php if ($paso < 4): ?>
            <!-- Enlaces adicionales -->
            <div class="auth-links">
                <a href="<?php echo APP_URL; ?>/views/auth/login.php">Volver a Iniciar Sesión</a>
                <?php if ($paso === 1): ?>
                    <a href="<?php echo APP_URL; ?>/views/auth/registro.php">¿No tienes cuenta? Regístrate</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>