<?php

// ============================================================================
// config/routes.php
// Definición de rutas del sistema
// ============================================================================

return [
    // Rutas públicas
    'GET' => [
        '/' => ['controller' => 'HomeController', 'action' => 'index'],
        '/login' => ['controller' => 'AuthController', 'action' => 'showLogin'],
        '/registro' => ['controller' => 'AuthController', 'action' => 'showRegistro'],
        '/recuperar-password' => ['controller' => 'AuthController', 'action' => 'showRecuperar'],
    ],
    
    'POST' => [
        '/login' => ['controller' => 'AuthController', 'action' => 'login'],
        '/registro' => ['controller' => 'AuthController', 'action' => 'registro'],
        '/recuperar-password' => ['controller' => 'AuthController', 'action' => 'recuperar'],
        '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    ],
    
    // Rutas de Cliente (requieren autenticación)
    'CLIENTE' => [
        '/dashboard' => ['controller' => 'ClienteController', 'action' => 'dashboard'],
        '/perfil' => ['controller' => 'ClienteController', 'action' => 'perfil'],
        '/polizas' => ['controller' => 'PolizaController', 'action' => 'lista'],
        '/polizas/{id}' => ['controller' => 'PolizaController', 'action' => 'detalle'],
        '/documentos/{id}' => ['controller' => 'DocumentoController', 'action' => 'descargar'],
    ],
    
    // Rutas Admin (requieren rol administrador)
    'ADMIN' => [
        '/admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard'],
        '/admin/clientes' => ['controller' => 'AdminController', 'action' => 'clientes'],
        '/admin/polizas' => ['controller' => 'AdminController', 'action' => 'polizas'],
        '/admin/pagos' => ['controller' => 'AdminController', 'action' => 'pagos'],
        '/admin/reportes' => ['controller' => 'AdminController', 'action' => 'reportes'],
    ]
];