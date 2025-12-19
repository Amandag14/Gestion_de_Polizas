<?php
// ============================================================================
// config/database.php
// Configuración de conexión a la base de datos con detección de entorno
// ============================================================================

class Database {
    private static $instance = null;
    private $connection;
    
    // Configuración según entorno
    private function getConfig() {
        // Detectar si estamos en desarrollo local o producción
        $isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) ||
                   in_array($_SERVER['SERVER_ADDR'], ['localhost', '127.0.0.1', '::1']);
        
        if ($isLocal) {
            // CONFIGURACIÓN LOCAL (XAMPP/WAMP/MAMP)
            return [
                'host' => 'localhost',
                'dbname' => 'portalseguro',
                'username' => 'root',
                'password' => '',  
                'charset' => 'utf8mb4',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            ];
        } else {
            // CONFIGURACIÓN PRODUCCIÓN (iPower)
            // ⚠️ CAMBIAR ESTOS VALORES CON TUS CREDENCIALES REALES
            return [
                'host' => 'localhost',
                'dbname' => 'portalseguro',  // iPower agrega prefijo
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_PERSISTENT => false  // Importante en hosting compartido
                ]
            ];
        }
    }
    
    private function __construct() {
        $config = $this->getConfig();
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // Log del error (no mostrar en producción)
            error_log("Error de conexión DB: " . $e->getMessage());
            
            // Mensaje genérico para el usuario
            die("Error de conexión a la base de datos. Contacte al administrador.");
        }
    }
    
    // Singleton - obtener instancia única
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener conexión PDO
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}