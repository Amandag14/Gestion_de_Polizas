<?php
/**
 * Archivo de Configuración Principal
 * Henriquez & Asociados - Sistema de Gestión de Pólizas
 */

// Detección de entorno
define('ENVIRONMENT', (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) ? 'development' : 'production');

// Configuración de errores según entorno
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Zona horaria
date_default_timezone_set('America/Panama');

// Configuración de URLs
define('BASE_URL', ENVIRONMENT === 'development' 
    ? 'http://localhost/henriquez-seguros/' 
    : 'https://tudominio.com/');

// Definir APP_URL si no está definida
if (!defined('APP_URL')) {
    define('APP_URL', ENVIRONMENT === 'development' 
        ? 'http://localhost/web_polizas' 
        : 'https://tudominio.com');
}

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('LOG_PATH', ROOT_PATH . '/logs/');

// Configuración de archivos
define('MAX_UPLOAD_SIZE', 10485760); // 10MB en bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);

// Configuración de sesión
define('SESSION_LIFETIME', 7200); // 2 horas en segundos
define('SESSION_NAME', 'HENRIQUEZ_SESSION');

// Configuración de email
define('EMAIL_FROM', 'info@henriquezyseguros.com');
define('EMAIL_FROM_NAME', 'Henríquez y Asociados');
define('EMAIL_REPLY_TO', 'info@henriquezyseguros.com');

// Información de la empresa
define('COMPANY_NAME', 'Henríquez y Asociados');
define('COMPANY_PHONE', '+507 XXXX-XXXX');
define('COMPANY_ADDRESS', 'Ciudad de Panamá, Panamá');

// Configuración de alertas
define('ALERT_DAYS_BEFORE_EXPIRY', 30); // Días antes de vencimiento para alertar

// Seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Paginación
define('RECORDS_PER_PAGE', 20);

// ==============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ==============================================

if (ENVIRONMENT === 'development') {
    // Configuración LOCAL (Desarrollo)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'web_polizas');  // ← CAMBIA este nombre a tu base de datos
    define('DB_USER', 'root');
    define('DB_PASS', '');  // En XAMPP/WAMP por defecto es vacío
    define('DB_CHARSET', 'utf8mb4');
} else {
    // Configuración PRODUCCIÓN
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'tu_base_datos_produccion');
    define('DB_USER', 'tu_usuario_produccion');
    define('DB_PASS', 'tu_contraseña_produccion');
    define('DB_CHARSET', 'utf8mb4');
}

/**
 * Función para obtener conexión a la base de datos
 * @return mysqli Objeto de conexión
 * @throws Exception Si falla la conexión
 */
function getDBConnection() {
    static $conn = null;
    
    // Si ya existe una conexión, reutilizarla
    if ($conn !== null && $conn->ping()) {
        return $conn;
    }
    
    try {
        // Crear nueva conexión
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Verificar errores de conexión
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }
        
        // Establecer charset
        if (!$conn->set_charset(DB_CHARSET)) {
            throw new Exception("Error al establecer charset: " . $conn->error);
        }
        
        return $conn;
        
    } catch (Exception $e) {
        // En desarrollo, mostrar el error completo
        if (ENVIRONMENT === 'development') {
            die("
                <div style='background:#fee; padding:20px; border-left:4px solid #c33; margin:20px;'>
                    <h2 style='color:#c33; margin:0 0 10px 0;'>❌ Error de Base de Datos</h2>
                    <p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>
                    <hr style='margin:15px 0; border:none; border-top:1px solid #ddd;'>
                    <p style='font-size:13px; color:#666;'>
                        <strong>Verifica:</strong><br>
                        1. Que XAMPP/WAMP esté ejecutándose<br>
                        2. Que MySQL esté activo<br>
                        3. Que la base de datos '<strong>" . DB_NAME . "</strong>' exista<br>
                        4. Usuario: '<strong>" . DB_USER . "</strong>' | Pass: '<strong>" . (DB_PASS ?: '(vacío)') . "</strong>'
                    </p>
                </div>
            ");
        } else {
            // En producción, registrar el error y mostrar mensaje genérico
            error_log("Error BD: " . $e->getMessage());
            die("Error de conexión al sistema. Contacte al administrador.");
        }
    }
}

/**
 * Función para cerrar la conexión a la base de datos
 */
function closeDBConnection() {
    static $conn = null;
    if ($conn !== null && $conn instanceof mysqli) {
        $conn->close();
        $conn = null;
    }
}

/**
 * Función auxiliar para ejecutar consultas preparadas de forma segura
 */
function executeQuery($conn, $sql, $params = [], $types = "") {
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $conn->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }
    
    return $stmt;
}

// Registrar función de cierre al finalizar el script
register_shutdown_function('closeDBConnection');
?>