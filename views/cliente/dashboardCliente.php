<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Obtener datos del usuario
$user_name = $_SESSION['user_name'] ?? 'Usuario';
$user_email = $_SESSION['user_email'] ?? '';
$ultimo_ingreso = $_SESSION['ultimo_ingreso'] ?? null;

// Formatear el último ingreso
$ultimo_ingreso_texto = 'Primer ingreso';
if ($ultimo_ingreso) {
    $fecha_ultimo = new DateTime($ultimo_ingreso);
    $fecha_actual = new DateTime();
    $diferencia = $fecha_actual->diff($fecha_ultimo);
    
    if ($diferencia->days > 0) {
        $ultimo_ingreso_texto = 'Último ingreso hace ' . $diferencia->days . ' día' . ($diferencia->days > 1 ? 's' : '');
    } elseif ($diferencia->h > 0) {
        $ultimo_ingreso_texto = 'Último ingreso hace ' . $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
    } elseif ($diferencia->i > 0) {
        $ultimo_ingreso_texto = 'Último ingreso hace ' . $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
    } else {
        $ultimo_ingreso_texto = 'Último ingreso hace unos segundos';
    }
}

// Obtener solo el primer nombre para el saludo
$nombre_parts = explode(' ', $user_name);
$primer_nombre = $nombre_parts[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - Henríquez & Asociados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .logo-badge {
            background: white;
            color: #004B93;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .header-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            padding: 0.5rem 0.8rem;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border-radius: 6px;
            min-width: 70px;
        }

        .header-btn:hover {
            background: rgba(255,255,255,0.15);
        }

        .header-btn i {
            font-size: 1.2rem;
            display: block;
        }

        .header-btn span {
            font-size: 0.7rem;
            font-weight: 500;
            display: block;
            white-space: nowrap;
        }

        .nav-menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .nav-left {
            display: flex;
            gap: 0;
        }

        .user-info {
            text-align: right;
            padding: 0.5rem 0;
        }

        .user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }

        .user-time {
            font-size: 0.7rem;
            opacity: 0.8;
            color: white;
        }

        .nav-item {
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            border-bottom-color: rgba(255,255,255,0.5);
        }

        .nav-item.active {
            border-bottom-color: white;
            background: rgba(255,255,255,0.1);
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 2rem;
        }

        .sidebar {
            background: white;
            border-radius: 8px;
            height: fit-content;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .sidebar-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a2e57;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: background 0.3s;
            user-select: none;
        }

        .sidebar-title:hover {
            background: #f8fafc;
        }

        .sidebar-title-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .toggle-icon {
            font-size: 1.2rem;
            transition: transform 0.3s;
            color: #64748b;
        }

        .toggle-icon.rotated {
            transform: rotate(180deg);
        }

        .sidebar-content {
            padding: 0 1.5rem 1.5rem;
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .sidebar-content.collapsed {
            max-height: 0;
            padding: 0 1.5rem;
        }

        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .shortcut-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .shortcut-item:hover {
            background: #f0f4f8;
            transform: translateY(-2px);
        }

        .shortcut-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0066B3 0%, #004B93 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .shortcut-label {
            font-size: 0.75rem;
            color: #1a2e57;
            font-weight: 500;
        }

        .main-content {
            min-width: 0;
        }

        .footer {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 1.5rem 2rem;
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-link {
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s;
        }

        .footer-link:hover {
            opacity: 0.8;
        }

        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .welcome-section h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #1e3c72;
        }

        .welcome-section p {
            color: #64748b;
            font-size: 0.95rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-left: 4px solid #3b82f6;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .stat-card.warning {
            border-left-color: #f59e0b;
        }

        .stat-card.success {
            border-left-color: #10b981;
        }

        .stat-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }

        .stat-subtitle {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            text-align: right;
            flex-shrink: 0;
        }

        .policies-section {
            background: #fff;
            border-radius: 6px;
            border: 1px solid #d9e1ed;
            overflow: hidden;
        }

        .section-header {
            background: #f3f6fb;
            padding: 12px 20px;
            border-bottom: 1px solid #d9e1ed;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 16px;
            color: #1a2e57;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            user-select: none;
        }

        .section-header:hover {
            background: #edf1f7;
        }

        .section-header-text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-toggle {
            font-size: 1.2rem;
            transition: transform 0.3s;
            color: #64748b;
        }

        .section-toggle.rotated {
            transform: rotate(180deg);
        }

        .policies-content {
            max-height: 3000px;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        .policies-content.collapsed {
            max-height: 0;
        }

        .table-header {
            background: #2f5597;
            color: white;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 60px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .policy-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 60px;
            padding: 14px 20px;
            border-bottom: 1px solid #e7ecf4;
            align-items: center;
            cursor: pointer;
        }

        .policy-row:hover {
            background: #f5f8fd;
        }

        .policy-name-cell .name {
            font-weight: 600;
            font-size: 14px;
            color: #003366;
            display: block;
        }

        .policy-name-cell .number {
            font-size: 13px;
            color: #7a8aa7;
            display: block;
        }

        .policy-value {
            color: #1a2e57;
            font-size: 14px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-vigente {
            background: #dff5e3;
            color: #237a3b;
        }

        .badge-vencer {
            background: #fff3cd;
            color: #946c00;
        }

        .more-options {
            font-size: 20px;
            text-align: center;
            color: #4a5b78;
        }

        .policy-details-expanded {
            background: #f9fbff;
            border-top: 1px solid #d9e1ed;
            padding: 20px;
            display: none;
        }

        .details-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .detail-group .label {
            color: #6c7b91;
            font-size: 12px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        .detail-group .value {
            font-size: 14px;
            color: #1a2e57;
            font-weight: 600;
            display: block;
        }

        .details-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            padding: 10px 16px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #2f5597;
            color: white;
        }

        .btn-primary:hover {
            background: #244178;
        }

        .btn-outline {
            border: 1px solid #2f5597;
            color: #2f5597;
            background: white;
        }

        .btn-outline:hover {
            background: #f0f4f8;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                gap: 1rem;
            }
            
            .stat-label {
                font-size: 0.7rem;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .sidebar {
                order: 2;
            }

            .main-content {
                order: 1;
            }
            
            .stats-grid {
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-label {
                font-size: 0.65rem;
            }
            
            .stat-value {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                padding: 0 1rem;
            }

            .nav-item {
                padding: 0.75rem 1rem;
                font-size: 0.85rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(4, minmax(140px, 1fr));
                gap: 0.5rem;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
            
            .stat-card {
                padding: 0.75rem;
                min-width: 140px;
            }
            
            .stat-label {
                font-size: 0.6rem;
            }
            
            .stat-value {
                font-size: 1.2rem;
            }
            
            .table-header {
                display: none;
            }
            
            .policy-row {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                position: relative;
                padding-right: 3rem;
            }
            
            .more-options {
                position: absolute;
                top: 1rem;
                right: 1rem;
            }

            .footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 0.75rem;
            }

            .shortcuts-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
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
                <a href="#" class="nav-item active">Pólizas</a>
                <a href="#" class="nav-item">Documentos</a>
                <a href="#" class="nav-item">Contacto</a>
                <a href="perfil.php" class="nav-item">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, <?php echo htmlspecialchars(strtoupper($user_name)); ?></div>
                <div class="user-time"><?php echo htmlspecialchars($ultimo_ingreso_texto); ?></div>
            </div>
        </nav>
    </header>

    <div class="container">
        <main class="main-content">
            <section class="welcome-section">
                <h1>Bienvenido, <?php echo htmlspecialchars($primer_nombre); ?></h1>
                <p>Aquí puedes consultar todas tus pólizas, pagos y documentos de forma rápida y segura.</p>
            </section>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-label">PÓLIZAS ACTIVAS</div>
                        <div class="stat-subtitle">Todas al día</div>
                    </div>
                    <div class="stat-value">5</div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-left">
                        <div class="stat-label">POR VENCER (30 DÍAS)</div>
                        <div class="stat-subtitle">Requiere atención</div>
                    </div>
                    <div class="stat-value">1</div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-left">
                        <div class="stat-label">PAGOS AL DÍA</div>
                        <div class="stat-subtitle">Sin pendientes</div>
                    </div>
                    <div class="stat-value">100%</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-label">PRIMA TOTAL ANUAL</div>
                        <div class="stat-subtitle">5 pólizas vigentes</div>
                    </div>
                    <div class="stat-value">$4,250</div>
                </div>
            </div>

            <section class="policies-section">
                <div class="section-header" onclick="togglePolicies()">
                    <div class="section-header-text">
                        Mis Pólizas
                    </div>
                    <span class="section-toggle" id="policiesToggle">
                        <i class="fas fa-chevron-up"></i>
                    </span>
                </div>
                
                <div class="policies-content" id="policiesContent">
                    <div class="table-header">
                        <div>Ramo de Póliza</div>
                        <div>Vigencia</div>
                        <div>Estado</div>
                        <div>Prima Anual</div>
                        <div></div>
                    </div>

                    <div class="policy-row" onclick="toggleDetails(1)">
                        <div class="policy-name-cell">
                            <span class="name">SEGURO DE VIDA INDIVIDUAL</span>
                            <span class="number">VID-2024-001234</span>
                        </div>
                        <div class="policy-value">10/01/24 - 10/01/25</div>
                        <div>
                            <span class="status-badge badge-vigente">Vigente</span>
                        </div>
                        <div class="policy-value">$1,200.00</div>
                        <div class="more-options">⋮</div>
                    </div>
                    <div class="policy-details-expanded" id="details-1">
                        <div class="details-content">
                            <div class="detail-group">
                                <span class="label">Asegurado</span>
                                <span class="value"><?php echo htmlspecialchars($user_name); ?></span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Compañía</span>
                                <span class="value">Aseguradora Mundial</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Suma Asegurada</span>
                                <span class="value">$100,000.00</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Frecuencia de Pago</span>
                                <span class="value">Anual</span>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-primary">Ver Detalles</button>
                            <button class="btn-action btn-outline">Descargar Póliza</button>
                            <button class="btn-action btn-outline">Hacer Pago</button>
                        </div>
                    </div>

                    <!-- Resto de pólizas aquí... -->
                    
                </div>
            </section>
        </main>

        <aside class="sidebar">
            <div class="sidebar-title" onclick="toggleSidebar()">
                <div class="sidebar-title-text">
                    <i class="fas fa-star"></i>
                    Accesos directos
                </div>
                <span class="toggle-icon" id="sidebarToggle">
                    <i class="fas fa-chevron-up"></i>
                </span>
            </div>
            <div class="sidebar-content" id="sidebarContent">
                <div class="shortcuts-grid">
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <span class="shortcut-label">Reportar Siniestro</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="shortcut-label">Realizar un Pago</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="shortcut-label">Contactar a mi corredor</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <span class="shortcut-label">Centro de Ayuda / FAQs</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <footer class="footer">
        <div class="footer-left">
            <span>Copyright © 2025 Henríquez & Asociados. Todos los derechos reservados.</span>
        </div>
        <div class="footer-links">
            <a href="#" class="footer-link">
                <i class="fas fa-shield-alt"></i>
                Seguridad
            </a>
            <a href="#" class="footer-link">
                <i class="fas fa-user-secret"></i>
                Privacidad
            </a>
            <a href="../auth/logout.php" class="footer-link">
                <i class="fas fa-sign-out-alt"></i>
                Salir
            </a>
        </div>
    </footer>

    <script>
        function togglePolicies() {
            const content = document.getElementById('policiesContent');
            const toggle = document.getElementById('policiesToggle');
            
            content.classList.toggle('collapsed');
            toggle.classList.toggle('rotated');
        }

        function toggleSidebar() {
            const content = document.getElementById('sidebarContent');
            const toggle = document.getElementById('sidebarToggle');
            
            content.classList.toggle('collapsed');
            toggle.classList.toggle('rotated');
        }

        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            
            document.querySelectorAll('.policy-details-expanded').forEach(detail => {
                if (detail.id !== `details-${id}`) {
                    detail.style.display = 'none';
                }
            });
            
            if (details.style.display === 'block') {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
            }
        }
    </script>
</body>
</html>