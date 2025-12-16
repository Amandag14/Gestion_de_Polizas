<?php
// core/Auth.php
// Sistema de autenticación con roles y permisos

class Auth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
        $this->iniciarSesion();
    }
    
    // Iniciar sesión de forma segura
    private function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            
            // Regenerar ID de sesión periódicamente para seguridad
            if (!isset($_SESSION['ultima_regeneracion'])) {
                $_SESSION['ultima_regeneracion'] = time();
            } elseif (time() - $_SESSION['ultima_regeneracion'] > 3600) {
                session_regenerate_id(true);
                $_SESSION['ultima_regeneracion'] = time();
            }
        }
    }
    
    // Login de cliente
    public function loginCliente($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, email, password_hash, nombre, apellido, 
                       estado_cuenta, aprobado, ejecutivo_id
                FROM clientes 
                WHERE email = :email AND aprobado = 1
            ");
            
            $stmt->execute(['email' => $email]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente && password_verify($password, $cliente['password_hash'])) {
                if ($cliente['estado_cuenta'] !== 'Activo') {
                    return ['success' => false, 'message' => 'Cuenta inactiva o suspendida'];
                }
                
                // Crear sesión
                $_SESSION['user_id'] = $cliente['id'];
                $_SESSION['user_type'] = 'cliente';
                $_SESSION['user_name'] = $cliente['nombre'] . ' ' . $cliente['apellido'];
                $_SESSION['user_email'] = $cliente['email'];
                $_SESSION['ejecutivo_id'] = $cliente['ejecutivo_id'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Actualizar último acceso
                $this->actualizarUltimoAcceso('clientes', $cliente['id']);
                
                // Registrar en log
                $this->registrarLog($cliente['id'], 'cliente', 'Login exitoso');
                
                return ['success' => true, 'redirect' => '/cliente/dashboard'];
            }
            
            return ['success' => false, 'message' => 'Credenciales incorrectas'];
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema'];
        }
    }
    
    // Login de usuario (admin/ejecutivo)
    public function loginUsuario($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.id, u.email, u.password_hash, u.nombre_completo, 
                       u.activo, r.nombre as rol
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.email = :email
            ");
            
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                if (!$usuario['activo']) {
                    return ['success' => false, 'message' => 'Usuario inactivo'];
                }
                
                // Crear sesión
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_type'] = 'usuario';
                $_SESSION['user_name'] = $usuario['nombre_completo'];
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_role'] = $usuario['rol'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Actualizar último acceso
                $this->actualizarUltimoAcceso('usuarios', $usuario['id']);
                
                // Registrar en log
                $this->registrarLog($usuario['id'], 'usuario', 'Login exitoso');
                
                $redirect = $usuario['rol'] === 'Administrador' ? '/admin/dashboard' : '/ejecutivo/dashboard';
                return ['success' => true, 'redirect' => $redirect];
            }
            
            return ['success' => false, 'message' => 'Credenciales incorrectas'];
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema'];
        }
    }
    
    // Registro de nuevo cliente
    public function registrarCliente($datos) {
        try {
            // Validar email único
            $stmt = $this->db->prepare("SELECT id FROM clientes WHERE email = :email");
            $stmt->execute(['email' => $datos['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'El email ya está registrado'];
            }
            
            // Insertar cliente
            $stmt = $this->db->prepare("
                INSERT INTO clientes (email, password_hash, cedula, nombre, apellido, 
                                     telefono, celular, direccion, provincia, tipo_cliente, aprobado)
                VALUES (:email, :password, :cedula, :nombre, :apellido, 
                        :telefono, :celular, :direccion, :provincia, :tipo_cliente, 0)
            ");
            
            $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            $stmt->execute([
                'email' => $datos['email'],
                'password' => $passwordHash,
                'cedula' => $datos['cedula'] ?? null,
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'] ?? '',
                'telefono' => $datos['telefono'] ?? null,
                'celular' => $datos['celular'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'provincia' => $datos['provincia'] ?? null,
                'tipo_cliente' => $datos['tipo_cliente'] ?? 'Personal'
            ]);
            
            return [
                'success' => true, 
                'message' => 'Registro exitoso. Su cuenta será activada por un administrador.'
            ];
            
        } catch (PDOException $e) {
            error_log("Error en registro: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al registrar'];
        }
    }
    
    // Verificar si usuario está autenticado
    public function estaAutenticado() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }
    
    // Verificar rol específico
    public function tieneRol($rol) {
        if (!$this->estaAutenticado()) {
            return false;
        }
        
        if ($_SESSION['user_type'] === 'usuario') {
            return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $rol;
        }
        
        return false;
    }
    
    // Verificar si es cliente
    public function esCliente() {
        return $this->estaAutenticado() && $_SESSION['user_type'] === 'cliente';
    }
    
    // Verificar si es admin
    public function esAdmin() {
        return $this->tieneRol('Administrador');
    }
    
    // Verificar si es ejecutivo
    public function esEjecutivo() {
        return $this->tieneRol('Ejecutivo');
    }
    
    // Obtener ID del usuario actual
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    // Obtener tipo de usuario
    public function getUserType() {
        return $_SESSION['user_type'] ?? null;
    }
    
    // Cerrar sesión
    public function logout() {
        if ($this->estaAutenticado()) {
            $this->registrarLog(
                $_SESSION['user_id'], 
                $_SESSION['user_type'], 
                'Logout'
            );
        }
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    // Verificar token CSRF
    public function verificarCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // Generar token de recuperación
    public function generarTokenRecuperacion($email, $tipoUsuario = 'cliente') {
        try {
            $tabla = $tipoUsuario === 'cliente' ? 'clientes' : 'usuarios';
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $this->db->prepare("
                UPDATE $tabla 
                SET token_recuperacion = :token, token_expiracion = :expiracion
                WHERE email = :email
            ");
            
            $stmt->execute([
                'token' => $token,
                'expiracion' => $expiracion,
                'email' => $email
            ]);
            
            if ($stmt->rowCount() > 0) {
                // Aquí enviarías el email con el link
                return ['success' => true, 'token' => $token];
            }
            
            return ['success' => false, 'message' => 'Email no encontrado'];
            
        } catch (PDOException $e) {
            error_log("Error generando token: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema'];
        }
    }
    
    // Recuperar contraseña
    public function recuperarPassword($token, $nuevaPassword, $tipoUsuario = 'cliente') {
        try {
            $tabla = $tipoUsuario === 'cliente' ? 'clientes' : 'usuarios';
            
            $stmt = $this->db->prepare("
                SELECT id FROM $tabla 
                WHERE token_recuperacion = :token 
                AND token_expiracion > NOW()
            ");
            
            $stmt->execute(['token' => $token]);
            $usuario = $stmt->fetch();
            
            if ($usuario) {
                $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("
                    UPDATE $tabla 
                    SET password_hash = :password,
                        token_recuperacion = NULL,
                        token_expiracion = NULL
                    WHERE id = :id
                ");
                
                $stmt->execute([
                    'password' => $passwordHash,
                    'id' => $usuario['id']
                ]);
                
                return ['success' => true, 'message' => 'Contraseña actualizada'];
            }
            
            return ['success' => false, 'message' => 'Token inválido o expirado'];
            
        } catch (PDOException $e) {
            error_log("Error recuperando password: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema'];
        }
    }
    
    // Actualizar último acceso
    private function actualizarUltimoAcceso($tabla, $id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE $tabla SET ultimo_acceso = NOW() WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Error actualizando acceso: " . $e->getMessage());
        }
    }
    
    // Registrar actividad en log
    private function registrarLog($userId, $tipoUsuario, $accion) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO logs_actividad 
                (usuario_id, cliente_id, tipo_usuario, accion, ip_address, user_agent)
                VALUES (:usuario_id, :cliente_id, :tipo_usuario, :accion, :ip, :user_agent)
            ");
            
            $stmt->execute([
                'usuario_id' => $tipoUsuario === 'usuario' ? $userId : null,
                'cliente_id' => $tipoUsuario === 'cliente' ? $userId : null,
                'tipo_usuario' => ucfirst($tipoUsuario),
                'accion' => $accion,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando log: " . $e->getMessage());
        }
    }
}

// core/Security.php
// Funciones de seguridad

class Security {
    
    // Sanitizar entrada
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    // Validar email
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validar cédula panameña
    public static function validarCedula($cedula) {
        // Formato: X-XXX-XXXX o XX-XXXX-XXXX
        return preg_match('/^\d{1,2}-\d{3,4}-\d{4}$/', $cedula);
    }
    
    // Validar fuerza de contraseña
    public static function validarPassword($password) {
        // Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }
    
    // Generar nombre seguro para archivos
    public static function nombreArchivoSeguro($nombreOriginal) {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombre = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $nombre = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombre);
        return uniqid() . '_' . $nombre . '.' . $extension;
    }
    
    // Validar tipo de archivo
    public static function validarTipoArchivo($archivo, $tiposPermitidos = ['pdf']) {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $mimeType = mime_content_type($archivo['tmp_name']);
        
        $mimePermitidos = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        
        return in_array($extension, $tiposPermitidos) && 
               in_array($mimeType, array_values($mimePermitidos));
    }
    
    // Proteger contra inyección SQL (usar con PDO prepared statements)
    public static function escaparSQL($string) {
        return addslashes($string);
    }
}
?>