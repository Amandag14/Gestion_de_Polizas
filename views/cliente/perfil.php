<?php
/**
 * Archivo: views/cliente/perfil.php
 * Vista de perfil del cliente - VERSIÓN CORREGIDA
 */

session_start();

// Verificar autenticación y rol
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 2) {
    header('Location: ../auth/login.php');
    exit();
}

// Configuración de errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $usuario_id = $_SESSION['user_id'];
    
    // Obtener información completa del usuario y cliente
    $query = "
        SELECT 
            u.id as usuario_id,
            u.email,
            u.ultimo_acceso,
            c.id as cliente_id,
            c.tipo_cliente,
            c.nombre,
            c.apellido,
            c.cedula,
            c.razon_social,
            c.ruc,
            c.telefono,
            c.celular,
            c.provincia,
            c.direccion,
            c.ejecutivo_id,
            c.created_at,
            c.updated_at
        FROM usuarios u
        INNER JOIN clientes c ON u.id = c.usuario_id
        WHERE u.id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$usuario_id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar que el cliente existe
    if (!$cliente) {
        session_destroy();
        header('Location: ../auth/login.php?error=cliente_no_encontrado');
        exit();
    }
    
    // Valores por defecto para estadísticas
    $stats = ['total_polizas' => 0, 'proximas_vencer' => 0];
    $prima_data = ['prima_total' => 0];
    
    // Intentar obtener estadísticas de pólizas (si la tabla existe)
    try {
        $query_stats = "
            SELECT 
                COUNT(*) as total_polizas,
                COUNT(CASE WHEN DATEDIFF(fecha_vencimiento, CURDATE()) <= 30 THEN 1 END) as proximas_vencer
            FROM polizas 
            WHERE cliente_id = ? AND estado = 'activa'
        ";
        $stmt_stats = $conn->prepare($query_stats);
        $stmt_stats->execute([$cliente['cliente_id']]);
        $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
        
        // Prima anual
        $query_prima = "
            SELECT COALESCE(SUM(prima), 0) as prima_total
            FROM polizas 
            WHERE cliente_id = ? AND estado = 'activa'
        ";
        $stmt_prima = $conn->prepare($query_prima);
        $stmt_prima->execute([$cliente['cliente_id']]);
        $prima_data = $stmt_prima->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // La tabla polizas no existe todavía, usar valores por defecto
    }
    
    // Calcular tiempo como cliente
    $fecha_registro = new DateTime($cliente['created_at']);
    $fecha_actual = new DateTime();
    $anos_cliente = $fecha_actual->diff($fecha_registro)->y;
    
    if ($anos_cliente == 0) {
        $meses_cliente = $fecha_actual->diff($fecha_registro)->m;
        $tiempo_cliente = $meses_cliente > 0 ? "$meses_cliente mes" . ($meses_cliente > 1 ? 'es' : '') : 'Nuevo cliente';
    } else {
        $tiempo_cliente = "$anos_cliente año" . ($anos_cliente > 1 ? 's' : '');
    }
    
    // Obtener ejecutivo asignado
    $ejecutivo = null;
    if (!empty($cliente['ejecutivo_id'])) {
        try {
            $query_ejecutivo = "
                SELECT e.*, u.email as ejecutivo_email
                FROM ejecutivos e
                LEFT JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.id = ?
            ";
            $stmt_ejecutivo = $conn->prepare($query_ejecutivo);
            $stmt_ejecutivo->execute([$cliente['ejecutivo_id']]);
            $ejecutivo = $stmt_ejecutivo->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Tabla ejecutivos no existe
        }
    }
    
    // Generar iniciales para avatar
    if ($cliente['tipo_cliente'] === 'Personal') {
        $nombre_display = $cliente['nombre'] . ' ' . $cliente['apellido'];
        $iniciales = strtoupper(substr($cliente['nombre'], 0, 1) . substr($cliente['apellido'], 0, 1));
    } else {
        $nombre_display = $cliente['razon_social'];
        $palabras = explode(' ', $cliente['razon_social']);
        $iniciales = strtoupper(
            substr($palabras[0], 0, 1) . 
            (isset($palabras[1]) ? substr($palabras[1], 0, 1) : substr($palabras[0], 1, 1))
        );
    }
    
    // Calcular tiempo desde último acceso
    if ($cliente['ultimo_acceso']) {
        $ultimo_acceso = new DateTime($cliente['ultimo_acceso']);
        $tiempo_transcurrido = $fecha_actual->diff($ultimo_acceso);
        
        if ($tiempo_transcurrido->i < 1) {
            $tiempo_texto = "Hace unos segundos";
        } elseif ($tiempo_transcurrido->i < 60) {
            $tiempo_texto = "Hace {$tiempo_transcurrido->i} minuto" . ($tiempo_transcurrido->i > 1 ? 's' : '');
        } elseif ($tiempo_transcurrido->h < 24) {
            $tiempo_texto = "Hace {$tiempo_transcurrido->h} hora" . ($tiempo_transcurrido->h > 1 ? 's' : '');
        } elseif ($tiempo_transcurrido->d < 30) {
            $tiempo_texto = "Hace {$tiempo_transcurrido->d} día" . ($tiempo_transcurrido->d > 1 ? 's' : '');
        } else {
            $tiempo_texto = $ultimo_acceso->format('d/m/Y H:i');
        }
    } else {
        $tiempo_texto = "Primer acceso";
    }
    
} catch (PDOException $e) {
    error_log("Error en perfil.php: " . $e->getMessage());
    die('Error al cargar el perfil. Por favor intenta más tarde. Detalles: ' . $e->getMessage());
} catch (Exception $e) {
    error_log("Error general en perfil.php: " . $e->getMessage());
    die('Error al cargar el perfil. Por favor intenta más tarde.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Henríquez y Asociados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copiar exactamente todos los estilos del documento 4 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #f5f7fa; color: #2c3e50; min-height: 100vh; display: flex; flex-direction: column; }
        .header { background: linear-gradient(135deg, #004B93 0%, #0066B3 100%); color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-top { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo { display: flex; align-items: center; gap: 1rem; font-size: 1.3rem; font-weight: 700; }
        .logo-badge { background: white; color: #004B93; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .header-buttons { display: flex; gap: 0.5rem; }
        .header-btn { display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.5rem 0.8rem; background: transparent; border: none; color: white; cursor: pointer; transition: all 0.3s; text-decoration: none; border-radius: 6px; min-width: 70px; }
        .header-btn:hover { background: rgba(255,255,255,0.15); }
        .header-btn i { font-size: 1.2rem; }
        .header-btn span { font-size: 0.7rem; font-weight: 500; }
        .nav-menu { display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .nav-left { display: flex; }
        .nav-item { padding: 1rem 1.5rem; color: rgba(255,255,255,0.9); text-decoration: none; font-size: 0.9rem; font-weight: 500; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .nav-item:hover { background: rgba(255,255,255,0.1); border-bottom-color: rgba(255,255,255,0.5); }
        .nav-item.active { border-bottom-color: white; background: rgba(255,255,255,0.1); }
        .user-info { text-align: right; padding: 0.5rem 0; }
        .user-name { font-size: 0.85rem; font-weight: 600; }
        .user-time { font-size: 0.7rem; opacity: 0.8; }
        .container { max-width: 100%; width: 100%; margin: 0; padding: 2rem; flex: 1; }
        .back-button { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: white; border: 1px solid #e2e8f0; border-radius: 6px; color: #475569; text-decoration: none; margin-bottom: 1.5rem; transition: all 0.3s; }
        .back-button:hover { background: #f8fafc; }
        .page-header { background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 2rem; }
        .profile-avatar-large { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); }
        .profile-header-info h1 { font-size: 2rem; color: #1e293b; margin-bottom: 0.5rem; }
        .profile-meta { display: flex; gap: 2rem; color: #64748b; font-size: 0.95rem; }
        .profile-meta-item { display: flex; align-items: center; gap: 0.5rem; }
        .card { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem; }
        .card-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 1.5rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .stat-box { text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 10px; border: 1px solid #e2e8f0; }
        .stat-box-value { font-size: 2rem; font-weight: 700; color: #004B93; margin-bottom: 0.25rem; }
        .stat-box-label { font-size: 0.85rem; color: #64748b; }
        .info-display { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .info-item { display: flex; flex-direction: column; gap: 0.25rem; }
        .info-item.full-width { grid-column: 1 / -1; }
        .info-label { font-size: 0.85rem; color: #94a3b8; font-weight: 500; }
        .info-value { font-size: 1rem; color: #1e293b; font-weight: 500; }
        .footer { background: linear-gradient(135deg, #004B93 0%, #0066B3 100%); color: white; padding: 1.5rem 2rem; margin-top: auto; text-align: center; }
        .hidden { display: none !important; }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .info-display { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo">
                Henríquez & Asociados
                <span class="logo-badge">Asesores de seguros</span>
            </div>

            <div class="header-buttons">
                <div class="header-btn">
                    <i class="fas fa-bell"></i>
                    <span>Mensajes</span>
                </div>
                <div class="header-btn">
                    <i class="fas fa-envelope"></i>
                    <span>Correo</span>
                </div>
                <a href="../auth/logout.php" class="header-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            </div>
        </div>

        <nav class="nav-menu">
            <div class="nav-left">
                <a href="dashboardCliente.php" class="nav-item">Pólizas</a>
                <a href="documentos.php" class="nav-item">Documentos</a>
                <a href="reclamos.php" class="nav-item">Reclamos</a>
                <a href="perfil.php" class="nav-item active">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, <?php echo strtoupper($nombre_display); ?></div>
                <div class="user-time"><?php echo $tiempo_texto; ?></div>
            </div>
        </nav>
    </header>

    <div class="container">
        <a href="dashboardCliente.php" class="back-button">← Volver al Inicio</a>

        <div class="page-header">
            <div class="profile-avatar-large"><?php echo $iniciales; ?></div>
            <div class="profile-header-info">
                <h1><?php echo htmlspecialchars($nombre_display); ?></h1>
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <span>📧</span>
                        <span><?php echo htmlspecialchars($cliente['email']); ?></span>
                    </div>
                    <div class="profile-meta-item">
                        <span>📱</span>
                        <span><?php echo htmlspecialchars($cliente['celular'] ?? 'No registrado'); ?></span>
                    </div>
                    <div class="profile-meta-item">
                        <span>📅</span>
                        <span>Cliente desde <?php echo date('M Y', strtotime($cliente['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Resumen de Cuenta</h3>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-box-value"><?php echo $stats['total_polizas']; ?></div>
                    <div class="stat-box-label">Pólizas Activas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value"><?php echo $anos_cliente; ?></div>
                    <div class="stat-box-label">Años como Cliente</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">$<?php echo number_format($prima_data['prima_total'], 2); ?></div>
                    <div class="stat-box-label">Prima Anual Total</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">👤 Información Personal</h3>
            <div class="info-display">
                <div class="info-item">
                    <span class="info-label">Nombre Completo</span>
                    <span class="info-value"><?php echo htmlspecialchars($nombre_display); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo de Cliente</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['tipo_cliente']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cédula/RUC</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['cedula'] ?? $cliente['ruc'] ?? 'No registrado'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['telefono'] ?? 'No registrado'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Celular</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['celular'] ?? 'No registrado'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Provincia</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['provincia'] ?? 'No registrada'); ?></span>
                </div>
                <div class="info-item full-width">
                    <span class="info-label">Dirección</span>
                    <span class="info-value"><?php echo htmlspecialchars($cliente['direccion'] ?? 'No registrada'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <span>Copyright © <?php echo date('Y'); ?> Henríquez & Asociados. Todos los derechos reservados.</span>
    </footer>
</body>
</html>