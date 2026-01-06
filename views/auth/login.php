<?php
session_start();

// Configuración de errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/database.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';
    $db = Database::getInstance()->getConnection();
    
    if ($form_type === 'login') {
        handleLogin($db, $mensaje, $tipo_mensaje);
    } elseif ($form_type === 'register') {
        handleRegister($db, $mensaje, $tipo_mensaje);
    }
}

/**
 * Maneja el proceso de inicio de sesión
 */
function handleLogin($db, &$mensaje, &$tipo_mensaje) {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validación de campos vacíos
    if (empty($email) || empty($password)) {
        $mensaje = 'Por favor completa todos los campos';
        $tipo_mensaje = 'error';
        return;
    }
    
    try {
        // Buscar usuario por email
        $stmt = $db->prepare("
            SELECT u.id, u.email, u.password_hash, u.rol_id, u.estado,
                   c.id as cliente_id, c.nombre, c.apellido, c.tipo_cliente
            FROM usuarios u
            LEFT JOIN clientes c ON u.id = c.usuario_id
            WHERE u.email = ? AND u.rol_id = 2
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar credenciales
        if (!$usuario) {
            $mensaje = 'Email o contraseña incorrectos';
            $tipo_mensaje = 'error';
            return;
        }
        
        if (!password_verify($password, $usuario['password_hash'])) {
            $mensaje = 'Email o contraseña incorrectos';
            $tipo_mensaje = 'error';
            return;
        }
        
        // Verificar estado del usuario
        if ($usuario['estado'] !== 'activo') {
            $mensaje = 'Tu cuenta está inactiva. Contacta al administrador.';
            $tipo_mensaje = 'error';
            return;
        }
        
        // Actualizar último acceso
        $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        
        // Establecer variables de sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['cliente_id'] = $usuario['cliente_id'];
        $_SESSION['user_name'] = trim($usuario['nombre'] . ' ' . $usuario['apellido']);
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['rol_id'] = $usuario['rol_id'];
        $_SESSION['tipo_cliente'] = $usuario['tipo_cliente'];
        $_SESSION['login_time'] = time();
        
        // Redirigir al dashboard del cliente
        header('Location: ../../views/cliente/dashboardCliente.php');
        exit;
        
    } catch (PDOException $e) {
        $mensaje = 'Error al iniciar sesión. Intenta de nuevo.';
        $tipo_mensaje = 'error';
        error_log("Error en login: " . $e->getMessage());
    }
}

/**
 * Maneja el proceso de registro
 */
function handleRegister($db, &$mensaje, &$tipo_mensaje) {
    // Obtener datos del formulario
    $tipo_cliente = $_POST['tipo_cliente'] ?? '';
    $email = filter_var($_POST['reg_email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['reg_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validaciones básicas
    if (empty($tipo_cliente) || empty($email) || empty($password)) {
        $mensaje = 'Por favor completa todos los campos obligatorios';
        $tipo_mensaje = 'error';
        return;
    }
    
    if ($password !== $confirm_password) {
        $mensaje = 'Las contraseñas no coinciden';
        $tipo_mensaje = 'error';
        return;
    }
    
    // Validación de contraseña robusta
    if (!validatePassword($password, $mensaje)) {
        $tipo_mensaje = 'error';
        return;
    }
    
    try {
        // Verificar si el email ya existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $mensaje = 'Este email ya está registrado';
            $tipo_mensaje = 'error';
            return;
        }
        
        // Validar campos según tipo de cliente
        $clienteData = validateClientData($tipo_cliente, $mensaje);
        if ($clienteData === false) {
            $tipo_mensaje = 'error';
            return;
        }
        
        // Iniciar transacción
        $db->beginTransaction();
        
        try {
            // Crear usuario
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("
                INSERT INTO usuarios (email, password_hash, rol_id, estado, created_at, updated_at)
                VALUES (?, ?, 2, 'activo', NOW(), NOW())
            ");
            $stmt->execute([$email, $password_hash]);
            $usuario_id = $db->lastInsertId();
            
            // Crear cliente
            $sql = "INSERT INTO clientes (
                usuario_id, tipo_cliente, nombre, apellido, cedula, razon_social, ruc,
                telefono, celular, provincia, direccion, fecha_registro, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $usuario_id,
                $clienteData['tipo_cliente'],
                $clienteData['nombre'],
                $clienteData['apellido'],
                $clienteData['cedula'],
                $clienteData['razon_social'],
                $clienteData['ruc'],
                $clienteData['telefono'],
                $clienteData['celular'],
                $clienteData['provincia'],
                $clienteData['direccion']
            ]);
            
            // Confirmar transacción
            $db->commit();
            
            $mensaje = 'Registro exitoso. Ya puedes iniciar sesión';
            $tipo_mensaje = 'success';
            
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
        }
        
    } catch (PDOException $e) {
        $mensaje = 'Error al registrar. Por favor intenta de nuevo.';
        $tipo_mensaje = 'error';
        error_log("Error en registro: " . $e->getMessage());
    }
}

/**
 * Valida la fortaleza de la contraseña
 */
function validatePassword($password, &$mensaje) {
    if (strlen($password) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres';
        return false;
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $mensaje = 'La contraseña debe contener al menos una letra mayúscula';
        return false;
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $mensaje = 'La contraseña debe contener al menos una letra minúscula';
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $mensaje = 'La contraseña debe contener al menos un número';
        return false;
    }
    
    if (!preg_match('/[@#$%&*!?]/', $password)) {
        $mensaje = 'La contraseña debe contener al menos un caracter especial (@#$%&*!?)';
        return false;
    }
    
    return true;
}

/**
 * Valida y obtiene los datos del cliente según su tipo
 */
function validateClientData($tipo_cliente, &$mensaje) {
    $data = [
        'tipo_cliente' => $tipo_cliente,
        'nombre' => '',
        'apellido' => '',
        'cedula' => null,
        'razon_social' => null,
        'ruc' => null,
        'telefono' => $_POST['telefono'] ?? null,
        'celular' => $_POST['celular'] ?? null,
        'provincia' => $_POST['provincia'] ?? null,
        'direccion' => $_POST['direccion'] ?? null
    ];
    
    if ($tipo_cliente === 'Personal') {
        $data['nombre'] = trim($_POST['nombre'] ?? '');
        $data['apellido'] = trim($_POST['apellido'] ?? '');
        $data['cedula'] = $_POST['cedula'] ?? null;
        
        if (empty($data['nombre']) || empty($data['apellido'])) {
            $mensaje = 'Nombre y apellido son obligatorios para clientes personales';
            return false;
        }
        
    } elseif ($tipo_cliente === 'Empresa') {
        $data['razon_social'] = trim($_POST['razon_social'] ?? '');
        $data['ruc'] = $_POST['ruc'] ?? null;
        
        if (empty($data['razon_social'])) {
            $mensaje = 'Razón social es obligatoria para empresas';
            return false;
        }
        
    } else {
        $mensaje = 'Tipo de cliente no válido';
        return false;
    }
    
    return $data;
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
            background: #f0f2f5;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: white;
            overflow: hidden;
        }

        .overlay-panel {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: #004B93;
            color: white;
            z-index: 100;
            transform: translateX(0);
            border-radius: 0 80px 80px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .container.register-mode .overlay-panel {
            transform: translateX(100%);
            border-radius: 80px 0 0 80px;
        }

        @keyframes slideToRegister {
            0% { transform: translateX(0); width: 50%; border-radius: 0 80px 80px 0; }
            50% { transform: translateX(0); width: 100%; border-radius: 0; }
            100% { transform: translateX(100%); width: 50%; border-radius: 80px 0 0 80px; }
        }

        @keyframes slideToLogin {
            0% { transform: translateX(100%); width: 50%; border-radius: 80px 0 0 80px; }
            50% { transform: translateX(0); width: 100%; border-radius: 0; }
            100% { transform: translateX(0); width: 50%; border-radius: 0 80px 80px 0; }
        }

        .container.animating-to-register .overlay-panel {
            animation: slideToRegister 1s ease-in-out forwards;
        }

        .container.animating-to-login .overlay-panel {
            animation: slideToLogin 1s ease-in-out forwards;
        }

        .overlay-content {
            position: relative;
            width: 100%;
            max-width: 500px;
            min-height: 300px;
        }

        .overlay-left, .overlay-right {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            text-align: center;
            transition: all 0.6s ease-in-out;
        }

        .overlay-left {
            opacity: 1;
            visibility: visible;
        }

        .overlay-right {
            opacity: 0;
            visibility: hidden;
        }

        .container.register-mode .overlay-left {
            opacity: 0;
            visibility: hidden;
        }

        .container.register-mode .overlay-right {
            opacity: 1;
            visibility: visible;
        }

        .overlay-panel h2 {
            font-size: 42px;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .overlay-panel p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .overlay-panel button {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 14px 50px;
            border-radius: 30px;
            font-size: 18px;
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

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
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
            align-items: flex-start;
        }

        .container.animating-to-register .form-container,
        .container.animating-to-login .form-container {
            opacity: 0;
        }

        .container.register-mode .login-container {
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        .container.register-mode .register-container {
            opacity: 1;
            z-index: 2;
            pointer-events: all;
        }

        .form-box {
            width: 100%;
            max-width: 450px;
            padding: 20px 30px;
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
            margin-bottom: 22px;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 15px 18px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .input-group input:focus, .input-group select:focus {
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
            margin-bottom: 25px;
            margin-top: 5px;
        }

        .forgot-password a {
            color: #004B93;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: #004B93;
            border: none;
            border-radius: 10px;
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

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
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

        .input-group.valid input, .input-group.valid select {
            border-color: #28a745;
        }

        .input-group.invalid input, .input-group.invalid select {
            border-color: #dc3545;
        }

        .input-feedback {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .input-feedback.show {
            display: block;
        }

        .input-feedback.error {
            color: #dc3545;
        }

        .input-feedback.success {
            color: #28a745;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background: #e8e8e8;
            overflow: hidden;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #28a745;
        }

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            padding: 10px;
            background: #f7f7f7;
            border-radius: 5px;
            display: none;
        }

        .password-requirements.show {
            display: block;
        }

        .password-requirements ul {
            margin: 5px 0 0 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin: 3px 0;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.invalid {
            color: #dc3545;
        }

        @media (max-width: 900px) {
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
                display: block !important;
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

            .form-box {
                max-width: 420px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        
        <!-- FORMULARIO DE LOGIN -->
        <div class="form-container login-container">
            <div class="form-box">
                <h3>Iniciar Sesión</h3>
                
                <?php if ($tipo_mensaje && $form_type === 'login'): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="submit-btn">Ingresar</button>
                </form>

                <div class="mobile-switch" style="display: none;">
                    <p>¿No tienes cuenta?</p>
                    <a onclick="showRegister()">Regístrate aquí</a>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE REGISTRO -->
        <div class="form-container register-container">
            <div class="form-box">
                <h3>Crear Cuenta</h3>
                
                <?php if ($tipo_mensaje && $form_type === 'register'): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>" id="registerAlert">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="registerForm">
                    <input type="hidden" name="form_type" value="register">
                    
                    <div class="input-group">
                        <label>Tipo de Cliente *</label>
                        <select name="tipo_cliente" id="tipo_cliente" required onchange="toggleCampos()">
                            <option value="">Seleccione...</option>
                            <option value="Personal">Personal</option>
                            <option value="Empresa">Empresa</option>
                        </select>
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
                    <div class="input-group">
                        <input type="email" name="reg_email" placeholder="Email *" required id="reg_email">
                        <div class="input-feedback" id="email-feedback"></div>
                    </div>
                    
                    <div class="input-row">
                        <div class="input-group">
                            <input type="tel" name="telefono" placeholder="Teléfono" id="telefono">
                        </div>
                        <div class="input-group">
                            <input type="tel" name="celular" placeholder="Celular *" required id="celular">
                            <div class="input-feedback" id="celular-feedback"></div>
                        </div>
                    </div>

                    <div class="input-group">
                        <select name="provincia">
                            <option value="">Provincia</option>
                            <option value="Panamá">Panamá</option>
                            <option value="Colón">Colón</option>
                            <option value="Chiriquí">Chiriquí</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <input type="text" name="direccion" placeholder="Dirección">
                    </div>

                    <div class="input-group">
                        <input type="password" name="reg_password" placeholder="Contraseña *" required minlength="8" id="reg_password">
                        <div class="password-strength" id="password-strength">
                            <div class="password-strength-bar" id="password-strength-bar"></div>
                        </div>
                        <div class="password-requirements" id="password-requirements">
                            <strong>La contraseña debe contener:</strong>
                            <ul>
                                <li id="req-length" class="invalid">Mínimo 8 caracteres</li>
                                <li id="req-uppercase" class="invalid">Una letra mayúscula</li>
                                <li id="req-lowercase" class="invalid">Una letra minúscula</li>
                                <li id="req-number" class="invalid">Un número</li>
                                <li id="req-special" class="invalid">Un carácter especial (@#$%&*)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="confirm_password" placeholder="Confirmar Contraseña *" required minlength="8" id="confirm_password">
                        <div class="input-feedback" id="confirm-feedback"></div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarme</button>
                </form>

                <div class="mobile-switch" style="display: none;">
                    <p>¿Ya tienes cuenta?</p>
                    <a onclick="showLogin()">Inicia sesión aquí</a>
                </div>
            </div>
        </div>

        <!-- Panel deslizante -->
        <div class="overlay-panel">
            <div class="overlay-content">
                <div class="overlay-left">
                    <h2>¡Hola, Bienvenido a Haseguros!</h2>
                    <p>¿No tienes una cuenta?</p>
                    <button type="button" onclick="showRegister()">Registrarse</button>
                </div>
                <div class="overlay-right">
                    <h2>¡Bienvenido de nuevo!</h2>
                    <p>¿Ya tienes una cuenta?</p>
                    <button type="button" onclick="showLogin()">Iniciar Sesión</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        let isTransitioning = false;

        function showRegister() {
            if (isTransitioning) return;
            isTransitioning = true;
            container.classList.add('animating-to-register');
            setTimeout(() => container.classList.add('register-mode'), 500);
            setTimeout(() => {
                container.classList.remove('animating-to-register');
                isTransitioning = false;
            }, 1000);
        }

        function showLogin() {
            if (isTransitioning) return;
            isTransitioning = true;
            container.classList.add('animating-to-login');
            setTimeout(() => container.classList.remove('register-mode'), 500);
            setTimeout(() => {
                container.classList.remove('animating-to-login');
                isTransitioning = false;
            }, 1000);
        }

        function toggleCampos() {
            const tipo = document.getElementById('tipo_cliente').value;
            const personal = document.getElementById('campos-personal');
            const empresa = document.getElementById('campos-empresa');
            
            personal.classList.remove('active');
            empresa.classList.remove('active');
            
            ['nombre', 'apellido', 'cedula', 'razon_social'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.required = false;
            });
            
            if (tipo === 'Personal') {
                personal.classList.add('active');
                document.getElementById('nombre').required = true;
                document.getElementById('apellido').required = true;
            } else if (tipo === 'Empresa') {
                empresa.classList.add('active');
                document.getElementById('razon_social').required = true;
            }
        }

        const regPassword = document.getElementById('reg_password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('password-strength');
        const passwordStrengthBar = document.getElementById('password-strength-bar');
        const passwordRequirements = document.getElementById('password-requirements');
        const confirmFeedback = document.getElementById('confirm-feedback');

        regPassword.addEventListener('focus', () => {
            passwordRequirements.classList.add('show');
            passwordStrength.classList.add('show');
        });

        regPassword.addEventListener('input', function() {
            const pwd = this.value;
            const checks = {
                length: pwd.length >= 8,
                uppercase: /[A-Z]/.test(pwd),
                lowercase: /[a-z]/.test(pwd),
                number: /[0-9]/.test(pwd),
                special: /[@#$%&*!?]/.test(pwd)
            };
            
            ['length', 'uppercase', 'lowercase', 'number', 'special'].forEach(req => {
                document.getElementById('req-' + req).className = checks[req] ? 'valid' : 'invalid';
            });

            const strength = Object.values(checks).filter(Boolean).length;
            passwordStrengthBar.className = 'password-strength-bar ' + 
                (strength <= 2 ? 'weak' : strength <= 4 ? 'medium' : 'strong');

            if (confirmPassword.value) validateConfirmPassword();
        });

        function validateConfirmPassword() {
            const pwd = regPassword.value;
            const confirm = confirmPassword.value;
            const parent = confirmPassword.parentElement;

            if (!confirm) {
                parent.classList.remove('valid', 'invalid');
                confirmFeedback.classList.remove('show');
                return;
            }

            if (pwd === confirm) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                confirmFeedback.className = 'input-feedback success show';
                confirmFeedback.textContent = '✓ Las contraseñas coinciden';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                confirmFeedback.className = 'input-feedback error show';
                confirmFeedback.textContent = '✗ Las contraseñas no coinciden';
            }
        }

        confirmPassword.addEventListener('input', validateConfirmPassword);

        document.getElementById('reg_email').addEventListener('blur', function() {
            const email = this.value;
            const parent = this.parentElement;
            const feedback = document.getElementById('email-feedback');
            if (!email) return;

            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                feedback.className = 'input-feedback success show';
                feedback.textContent = '✓ Email válido';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                feedback.className = 'input-feedback error show';
                feedback.textContent = '✗ Email inválido';
            }
        });

        const celular = document.getElementById('celular');
        celular.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d-]/g, '');
        });

        celular.addEventListener('blur', function() {
            const phone = this.value;
            const parent = this.parentElement;
            const feedback = document.getElementById('celular-feedback');
            if (!phone) return;

            if (/^\d{4}-?\d{4}$/.test(phone.replace(/-/g, ''))) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                feedback.className = 'input-feedback success show';
                feedback.textContent = '✓ Número válido';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                feedback.className = 'input-feedback error show';
                feedback.textContent = '✗ Formato: XXXX-XXXX';
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const pwd = regPassword.value;
            const confirm = confirmPassword.value;

            if (pwd.length < 8 || !/[A-Z]/.test(pwd) || !/[a-z]/.test(pwd) || 
                !/[0-9]/.test(pwd) || !/[@#$%&*!?]/.test(pwd)) {
                e.preventDefault();
                alert('La contraseña no cumple con todos los requisitos de seguridad.');
                regPassword.focus();
                return false;
            }

            if (pwd !== confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                confirmPassword.focus();
                return false;
            }
        });
        
        <?php if ($tipo_mensaje === 'success' && $form_type === 'register'): ?>
            // Auto-switch a login después de registro exitoso
            setTimeout(() => {
                showLogin();
                // Limpiar el formulario
                document.getElementById('registerForm').reset();
                toggleCampos();
            }, 2500);
        <?php endif; ?>
        
        <?php if ($tipo_mensaje && $form_type === 'register'): ?>
            // Ocultar alerta después de 5 segundos
            setTimeout(() => {
                const alert = document.getElementById('registerAlert');
                if (alert) {
                    alert.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>