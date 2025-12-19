<?php
// ============================================================================
// config/session.php
// Configuración segura de sesiones
// ============================================================================

// Configuración de seguridad para cookies de sesión
ini_set('session.cookie_httponly', 1);      // Previene acceso via JavaScript
ini_set('session.use_only_cookies', 1);     // Solo cookies, no URL
ini_set('session.cookie_samesite', 'Strict'); // Protección CSRF
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// En producción, forzar HTTPS
if (ENVIRONMENT === 'production') {
    ini_set('session.cookie_secure', 1);    // Solo sobre HTTPS
}

// Nombre de sesión personalizado
session_name(SESSION_NAME);

// Función para iniciar sesión de forma segura
function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        
        // Validar sesión existente
        if (isset($_SESSION['created_at'])) {
            // Regenerar ID cada 30 minutos
            if (time() - $_SESSION['created_at'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created_at'] = time();
            }
        } else {
            $_SESSION['created_at'] = time();
        }
        
        // Validar IP del usuario (opcional, puede causar problemas con IPs dinámicas)
        if (!isset($_SESSION['user_ip'])) {
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        } elseif ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
            // IP cambió, posible sesión robada
            session_destroy();
            session_start();
        }
    }
}

// Función para validar timeout de sesión
function validarTimeoutSesion() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        
        if ($inactive > SESSION_LIFETIME) {
            session_destroy();
            session_start();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}