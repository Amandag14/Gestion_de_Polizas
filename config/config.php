<?php
// ============================================================================
// config/config.php
// Configuración general del sistema
// ============================================================================

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
    ? 'http://localhost/Gestion_de_Polizas/' 
    : 'https://tudominio.com/');

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