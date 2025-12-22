<?php
/**
 * Archivo: views/auth/registro.php
 * Descripción: Registro de nuevos clientes
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Definir APP_URL
define('APP_URL', 'http://localhost/Gestion_de_Polizas');

$mensaje = "";
$tipo_mensaje = "";
$registro_exitoso = false;

/**
 * Obtener conexión a la base de datos
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
    if (strlen($password) < 8) {
        throw new Exception("La contraseña debe tener al menos 8 caracteres.");
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

// Procesar formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = getDBConnection();
        
        // Obtener y validar datos del formulario
        $tipo_cliente = $_POST['tipo_cliente'] ?? '';
        $email = validarEmail($_POST['email'] ?? '');
        $password = validarPassword($_POST['password'] ?? '');
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
        $aprobado = 1; // 1 = auto-aprobación, 0 = requiere aprobación manual
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
            $registro_exitoso = true;
            $tipo_mensaje = "success";
            
            if ($aprobado == 1) {
                $mensaje = "¡Registro exitoso! Puedes iniciar sesión ahora.";
            } else {
                $mensaje = "¡Registro exitoso! Tu cuenta será revisada por un administrador.";
            }
            
            // Log de registro (opcional)
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
    <title>Registro - Henriquez & Asociados</title>
    <link rel="stylesheet" href="/Gestion_de_Polizas/public/css/auth/registro.css">
</head>
<body>
    <div class="auth-container">
        <!-- Logo y Nombre de la Empresa -->
        <div class="logo-container">
            <img src="/Gestion_de_Polizas/public/img/HaLogo-1.png" alt="Logo Henriquez & Asociados" class="logo-ha"
                 onerror="this.style.display='none'">
        </div>

        <!-- Header del Registro -->
        <div class="login-header">
            <h1>Crear Nueva Cuenta</h1>
            <p>Registra tus datos para acceder al sistema</p>
        </div>

        <!-- Mensajes de Error/Éxito -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $tipo_mensaje === 'error' ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            
            <?php if ($registro_exitoso): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = '<?php echo APP_URL; ?>/views/auth/login.php';
                    }, 3000);
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Formulario de Registro -->
        <form id="registroForm" method="POST" action="">
            <!-- Tipo de Cliente -->
            <div class="form-group">
                <label for="tipoCliente">Tipo de Cliente *</label>
                <select id="tipoCliente" name="tipo_cliente" required>
                    <option value="">Selecciona una opción</option>
                    <option value="Personal" <?php echo (isset($_POST['tipo_cliente']) && $_POST['tipo_cliente'] === 'Personal') ? 'selected' : ''; ?>>Personal</option>
                    <option value="Empresa" <?php echo (isset($_POST['tipo_cliente']) && $_POST['tipo_cliente'] === 'Empresa') ? 'selected' : ''; ?>>Empresa</option>
                </select>
            </div>

            <!-- Campos para Cliente Personal -->
            <div id="personalFields" <?php echo (isset($_POST['tipo_cliente']) && $_POST['tipo_cliente'] === 'Empresa') ? 'class="hidden"' : ''; ?>>
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Juan Carlos" 
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Delgado"
                           value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="cedula">Cédula *</label>
                    <input type="text" id="cedula" name="cedula" placeholder="8-123-4567" pattern="[0-9]{1,2}-[0-9]{3,4}-[0-9]{4}"
                           value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
                    <small class="input-help">Formato: X-XXX-XXXX</small>
                </div>
            </div>

            <!-- Campos para Empresa -->
            <div id="empresaFields" <?php echo (!isset($_POST['tipo_cliente']) || $_POST['tipo_cliente'] !== 'Empresa') ? 'class="hidden"' : ''; ?>>
                <div class="form-group">
                    <label for="razonSocial">Razón Social *</label>
                    <input type="text" id="razonSocial" name="razon_social" placeholder="Empresa S.A."
                           value="<?php echo htmlspecialchars($_POST['razon_social'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="ruc">RUC (opcional)</label>
                    <input type="text" id="ruc" name="ruc" placeholder="12345-67-890123"
                           value="<?php echo htmlspecialchars($_POST['ruc'] ?? ''); ?>">
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <small class="strength-text" id="strengthText">Ingresa una contraseña</small>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmar Contraseña *</label>
                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Repite tu contraseña" required>
            </div>

            <!-- Teléfonos -->
            <div class="form-group">
                <label for="telefono">Teléfono (opcional)</label>
                <input type="tel" id="telefono" name="telefono" placeholder="+507 XXXX-XXXX"
                       value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="celular">Celular *</label>
                <input type="tel" id="celular" name="celular" placeholder="+507 6XXX-XXXX" required
                       value="<?php echo htmlspecialchars($_POST['celular'] ?? ''); ?>">
            </div>

            <!-- Dirección -->
            <div class="form-group">
                <label for="provincia">Provincia</label>
                <select id="provincia" name="provincia">
                    <option value="">Selecciona una provincia</option>
                    <option value="Panama" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Panama') ? 'selected' : ''; ?>>Panamá</option>
                    <option value="Colon" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Colon') ? 'selected' : ''; ?>>Colón</option>
                    <option value="Chiriqui" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Chiriqui') ? 'selected' : ''; ?>>Chiriquí</option>
                    <option value="Bocas del Toro" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Bocas del Toro') ? 'selected' : ''; ?>>Bocas del Toro</option>
                    <option value="Veraguas" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Veraguas') ? 'selected' : ''; ?>>Veraguas</option>
                    <option value="Cocle" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Cocle') ? 'selected' : ''; ?>>Coclé</option>
                    <option value="Herrera" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Herrera') ? 'selected' : ''; ?>>Herrera</option>
                    <option value="Los Santos" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Los Santos') ? 'selected' : ''; ?>>Los Santos</option>
                    <option value="Darien" <?php echo (isset($_POST['provincia']) && $_POST['provincia'] === 'Darien') ? 'selected' : ''; ?>>Darién</option>
                </select>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion" placeholder="Calle, edificio, número de casa..."><?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?></textarea>
            </div>

            <!-- Términos y Condiciones -->
            <div class="checkbox-group">
                <input type="checkbox" id="terminos" name="terminos" required>
                <label for="terminos">
                    Acepto los <a href="#" target="_blank">Términos y Condiciones</a> 
                    y la <a href="#" target="_blank">Política de Privacidad</a>
                </label>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" class="btn btn-primary" id="submitBtn">
                Crear Cuenta
            </button>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-links">
            <a href="<?php echo APP_URL; ?>/views/auth/login.php">¿Ya tienes cuenta? Inicia Sesión</a>
        </div>
    </div>

    <script>
        const registroForm = document.getElementById('registroForm');
        const tipoCliente = document.getElementById('tipoCliente');
        const personalFields = document.getElementById('personalFields');
        const empresaFields = document.getElementById('empresaFields');

        // Cambiar campos según tipo de cliente
        tipoCliente.addEventListener('change', function() {
            if (this.value === 'Empresa') {
                personalFields.classList.add('hidden');
                empresaFields.classList.remove('hidden');
                
                document.getElementById('nombre').required = false;
                document.getElementById('apellido').required = false;
                document.getElementById('cedula').required = false;
                document.getElementById('razonSocial').required = true;
            } else if (this.value === 'Personal') {
                personalFields.classList.remove('hidden');
                empresaFields.classList.add('hidden');
                
                document.getElementById('nombre').required = true;
                document.getElementById('apellido').required = true;
                document.getElementById('cedula').required = true;
                document.getElementById('razonSocial').required = false;
            }
        });

        // Medidor de fortaleza de contraseña
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthFill.className = 'strength-fill';
            
            if (strength === 0) {
                strengthText.textContent = 'Muy débil';
            } else if (strength <= 2) {
                strengthFill.classList.add('weak');
                strengthText.textContent = 'Débil';
            } else if (strength === 3) {
                strengthFill.classList.add('medium');
                strengthText.textContent = 'Media';
            } else {
                strengthFill.classList.add('strong');
                strengthText.textContent = '¡Fuerte!';
            }
        });

        // Validar contraseñas
        document.getElementById('confirmPassword').addEventListener('blur', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>