<?php
// ============================================================================
// config/constants.php
// Constantes del sistema
// ============================================================================

// Estados de pólizas
define('POLIZA_VIGENTE', 'Vigente');
define('POLIZA_POR_VENCER', 'Por Vencer');
define('POLIZA_VENCIDA', 'Vencida');
define('POLIZA_CANCELADA', 'Cancelada');
define('POLIZA_RENOVADA', 'Renovada');

// Estados de pagos
define('PAGO_PENDIENTE', 'Pendiente');
define('PAGO_PAGADO', 'Pagado');
define('PAGO_ATRASADO', 'Atrasado');
define('PAGO_CANCELADO', 'Cancelado');

// Estados de clientes
define('CLIENTE_ACTIVO', 'Activo');
define('CLIENTE_INACTIVO', 'Inactivo');
define('CLIENTE_SUSPENDIDO', 'Suspendido');

// Roles de usuario
define('ROL_ADMIN', 'Administrador');
define('ROL_EJECUTIVO', 'Ejecutivo');
define('ROL_CLIENTE', 'Cliente');

// Tipos de documentos
define('DOC_POLIZA', 'Póliza');
define('DOC_ENDOSO', 'Endoso');
define('DOC_RECIBO', 'Recibo');
define('DOC_CERTIFICADO', 'Certificado');
define('DOC_FACTURA', 'Factura');
define('DOC_COMUNICACION', 'Comunicación');
define('DOC_OTRO', 'Otro');

// Métodos de pago
define('PAGO_TRANSFERENCIA', 'Transferencia');
define('PAGO_CHEQUE', 'Cheque');
define('PAGO_EFECTIVO', 'Efectivo');
define('PAGO_TARJETA', 'Tarjeta');
define('PAGO_ACH', 'ACH');
define('PAGO_YAPPY', 'Yappy');

// Tipos de alertas
define('ALERTA_VENCIMIENTO', 'Vencimiento');
define('ALERTA_PAGO', 'Pago');
define('ALERTA_RENOVACION', 'Renovación');
define('ALERTA_GENERAL', 'General');

// Mensajes del sistema
define('MSG_LOGIN_SUCCESS', 'Bienvenido al sistema');
define('MSG_LOGIN_ERROR', 'Credenciales incorrectas');
define('MSG_SESSION_EXPIRED', 'Tu sesión ha expirado');
define('MSG_ACCESS_DENIED', 'No tienes permisos para acceder a esta página');
define('MSG_SAVE_SUCCESS', 'Datos guardados correctamente');
define('MSG_SAVE_ERROR', 'Error al guardar los datos');
define('MSG_DELETE_SUCCESS', 'Registro eliminado correctamente');
define('MSG_DELETE_ERROR', 'Error al eliminar el registro');
