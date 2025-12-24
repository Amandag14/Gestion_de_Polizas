<?php
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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

        /* Panel de bienvenida (lado derecho para registro) */
        .welcome-panel {
            position: absolute;
            right: 0;
            top: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, #0097b2 0%, #00a8cc 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            z-index: 2;
            transition: transform 0.6s ease-in-out;
        }

        .welcome-panel h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .welcome-panel p {
            font-size: 16px;
            margin-bottom: 30px;
            text-align: center;
            opacity: 0.9;
        }

        .welcome-panel button {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 40px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .welcome-panel button:hover {
            background: white;
            color: #00a8cc;
        }

        /* Panel de formularios */
        .forms-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            transition: transform 0.6s ease-in-out;
        }

        .form-box {
            width: 100%;
        }

        .form-box h3 {
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #00a8cc;
            background: white;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .input-group small {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 11px;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00a8cc 0%, #0097b2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 168, 204, 0.4);
        }

        .social-login {
            margin-top: 20px;
            text-align: center;
        }

        .social-login p {
            color: #999;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            border-color: #00a8cc;
            color: #00a8cc;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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
                height: 100vh;
                border-radius: 0;
            }

            .welcome-panel,
            .forms-container {
                width: 100%;
            }

            .welcome-panel {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Panel de Formulario de Registro -->
        <div class="forms-container" id="formsContainer">
            <div class="form-box">
                <h3>Registration</h3>
                
                <!-- Mensajes de alerta -->
                <div id="alertContainer"></div>

                <form method="POST" action="">
                    <div class="input-group">
                        <i>👤</i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    
                    <div class="input-group">
                        <i>✉️</i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <i>🔒</i>
                        <input type="password" name="password" placeholder="Password" required>
                        <small>Please fill out this field</small>
                    </div>
                    
                    <button type="submit" class="submit-btn">Register</button>
                </form>

                <div class="social-login">
                    <p>or register with social platforms</p>
                    <div class="social-icons">
                        <a href="#"><span>G</span></a>
                        <a href="#"><span>f</span></a>
                        <a href="#"><span>⚡</span></a>
                        <a href="#"><span>in</span></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Bienvenida (lado derecho) -->
        <div class="welcome-panel" id="welcomePanel">
            <h2>Welcome Back!</h2>
            <p>Already have an Account?</p>
            <button onclick="goToLogin()">Login</button>
        </div>
    </div>

    <script>
        function goToLogin() {
            window.location.href = '/Gestion_de_Polizas/views/auth/login.php';
        }
    </script>
</body>
</html>