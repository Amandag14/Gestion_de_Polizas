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

        /* Botones de header unificados */
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

        /* Container */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 2rem;
        }

        /* Sidebar - Accesos Directos CON TOGGLE */
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

        /* Footer */
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

        .footer-logo {
            font-size: 1.5rem;
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

        /* Welcome Section */
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

        /* Stats Cards - SIEMPRE 4 COLUMNAS FIJAS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #3b82f6;
            min-width: 0;
        }

        .stat-card.warning {
            border-left-color: #f59e0b;
        }

        .stat-card.success {
            border-left-color: #10b981;
        }

        .stat-card.danger {
            border-left-color: #ef4444;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* --- ESTILO TIPO BANCO GENERAL CON TOGGLE --- */

        .policies-section {
            background: #fff;
            border-radius: 6px;
            border: 1px solid #d9e1ed;
            overflow: hidden;
        }

        /* Encabezado con toggle */
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

        /* Header tabla */
        .table-header {
            background: #2f5597;
            color: white;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 60px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Filas */
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

        /* Badges estilo banco */
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

        /* Botón de opciones */
        .more-options {
            font-size: 20px;
            text-align: center;
            color: #4a5b78;
        }

        /* Expanded details estilo tabla del banco */
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

        /* Responsive */
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
            
            .stat-subtitle {
                font-size: 0.75rem;
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
            
            /* Stats Cards - Con scroll horizontal en móvil */
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
            
            .stat-subtitle {
                font-size: 0.7rem;
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
            
            .details-content {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }
            
            .details-actions {
                flex-direction: column;
                padding: 0 1.5rem 1.5rem;
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

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: repeat(4, minmax(120px, 1fr));
                gap: 0.4rem;
            }
            
            .stat-card {
                padding: 0.6rem;
                min-width: 120px;
            }
            
            .stat-label {
                font-size: 0.55rem;
            }
            
            .stat-value {
                font-size: 1.1rem;
            }
            
            .stat-subtitle {
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
                <a href="http://localhost/Gestion_de_Polizas/views/auth/logout.php" class="header-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            </div>
        </div>
        
        <nav class="nav-menu">
            <div class="nav-left">
                <a href="#" class="nav-item active">Pólizas</a>
                <a href="#" class="nav-item">Consultas</a>
                <a href="#" class="nav-item">Transacciones</a>
                <a href="#" class="nav-item">Recargas</a>
                <a href="#" class="nav-item">Solicitudes</a>
                <a href="http://localhost/Gestion_de_Polizas/views/cliente/perfil.php" class="nav-item">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, JUAN DELGADO</div>
                <div class="user-time">Último ingreso hace 14 minutos</div>
            </div>
        </nav>
    </header>

    <!-- Container -->
    <div class="container">
        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Section -->
            <section class="welcome-section">
                <h1>Bienvenido, Juan</h1>
                <p>Aquí puedes consultar todas tus pólizas, pagos y documentos de forma rápida y segura.</p>
            </section>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">PÓLIZAS ACTIVAS</div>
                    <div class="stat-value">5</div>
                    <div class="stat-subtitle">Todas al día</div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-label">POR VENCER (30 DÍAS)</div>
                    <div class="stat-value">1</div>
                    <div class="stat-subtitle">Requiere atención</div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-label">PAGOS AL DÍA</div>
                    <div class="stat-value">100%</div>
                    <div class="stat-subtitle">Sin pendientes</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">PRIMA TOTAL ANUAL</div>
                    <div class="stat-value">$4,250</div>
                    <div class="stat-subtitle">5 pólizas vigentes</div>
                </div>
            </div>

            <!-- Mis Pólizas CON TOGGLE -->
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

                    <!-- Policy Row 1 -->
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
                                <span class="value">Juan Delgado Pérez</span>
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

                    <!-- Policy Row 2 -->
                    <div class="policy-row" onclick="toggleDetails(2)">
                        <div class="policy-name-cell">
                            <span class="name">SEGURO DE VEHÍCULO</span>
                            <span class="number">AUT-2024-005678</span>
                        </div>
                        <div class="policy-value">15/12/24 - 15/12/25</div>
                        <div>
                            <span class="status-badge badge-vencer">Por Vencer</span>
                        </div>
                        <div class="policy-value">$850.00</div>
                        <div class="more-options">⋮</div>
                    </div>
                    <div class="policy-details-expanded" id="details-2">
                        <div class="details-content">
                            <div class="detail-group">
                                <span class="label">Vehículo</span>
                                <span class="value">Toyota Corolla 2022</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Placa</span>
                                <span class="value">ABC-1234</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Cobertura</span>
                                <span class="value">Todo Riesgo</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Deducible</span>
                                <span class="value">$500.00</span>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-primary">Ver Detalles</button>
                            <button class="btn-action btn-outline">Renovar Póliza</button>
                            <button class="btn-action btn-outline">Reportar Siniestro</button>
                        </div>
                    </div>

                    <!-- Policy Row 3 -->
                    <div class="policy-row" onclick="toggleDetails(3)">
                        <div class="policy-name-cell">
                            <span class="name">SEGURO DE GASTOS MÉDICOS</span>
                            <span class="number">SAL-2024-009012</span>
                        </div>
                        <div class="policy-value">01/03/24 - 01/03/25</div>
                        <div>
                            <span class="status-badge badge-vigente">Vigente</span>
                        </div>
                        <div class="policy-value">$2,160.00</div>
                        <div class="more-options">⋮</div>
                    </div>
                    <div class="policy-details-expanded" id="details-3">
                        <div class="details-content">
                            <div class="detail-group">
                                <span class="label">Plan</span>
                                <span class="value">Premium Plus</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Red de Hospitales</span>
                                <span class="value">Nacional</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Cobertura Anual</span>
                                <span class="value">$500,000.00</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Copago</span>
                                <span class="value">20%</span>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-primary">Ver Detalles</button>
                            <button class="btn-action btn-outline">Red de Médicos</button>
                            <button class="btn-action btn-outline">Hacer Reclamo</button>
                        </div>
                    </div>

                    <!-- Policy Row 4 -->
                    <div class="policy-row" onclick="toggleDetails(4)">
                        <div class="policy-name-cell">
                            <span class="name">SEGURO DE HOGAR</span>
                            <span class="number">HOG-2024-003456</span>
                        </div>
                        <div class="policy-value">20/06/24 - 20/06/25</div>
                        <div>
                            <span class="status-badge badge-vigente">Vigente</span>
                        </div>
                        <div class="policy-value">$620.00</div>
                        <div class="more-options">⋮</div>
                    </div>
                    <div class="policy-details-expanded" id="details-4">
                        <div class="details-content">
                            <div class="detail-group">
                                <span class="label">Dirección</span>
                                <span class="value">Calle Principal #123</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Tipo de Propiedad</span>
                                <span class="value">Casa</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Valor Asegurado</span>
                                <span class="value">$250,000.00</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Contenidos</span>
                                <span class="value">$50,000.00</span>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-primary">Ver Detalles</button>
                            <button class="btn-action btn-outline">Actualizar Inventario</button>
                            <button class="btn-action btn-outline">Reportar Siniestro</button>
                        </div>
                    </div>

                    <!-- Policy Row 5 -->
                    <div class="policy-row" onclick="toggleDetails(5)">
                        <div class="policy-name-cell">
                            <span class="name">SEGURO DE INCENDIO COMERCIAL</span>
                            <span class="number">COM-2024-007890</span>
                        </div>
                        <div class="policy-value">05/08/24 - 05/08/25</div>
                        <div>
                            <span class="status-badge badge-vigente">Vigente</span>
                        </div>
                        <div class="policy-value">$1,420.00</div>
                        <div class="more-options">⋮</div>
                    </div>
                    <div class="policy-details-expanded" id="details-5">
                        <div class="details-content">
                            <div class="detail-group">
                                <span class="label">Establecimiento</span>
                                <span class="value">Tienda La Economía</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Giro Comercial</span>
                                <span class="value">Retail</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Suma Asegurada</span>
                                <span class="value">$300,000.00</span>
                            </div>
                            <div class="detail-group">
                                <span class="label">Cobertura</span>
                                <span class="value">Incendio y Terremoto</span>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-primary">Ver Detalles</button>
                            <button class="btn-action btn-outline">Certificado</button>
                            <button class="btn-action btn-outline">Contactar Agente</button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Sidebar - Accesos Directos CON TOGGLE -->
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
                        <span class="shortcut-label">Entre cuentas</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="shortcut-label">Favoritos</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="shortcut-label">A Terceros</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <span class="shortcut-label">Pagos</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <span class="shortcut-label">Transporte</span>
                    </div>
                    <div class="shortcut-item">
                        <div class="shortcut-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <span class="shortcut-label">Telefonía</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Footer -->
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
            <a href="http://localhost/Gestion_de_Polizas/views/auth/logout.php" class="footer-link">
                <i class="fas fa-sign-out-alt"></i>
                Salir
            </a>
        </div>
    </footer>

    <script>
        // Toggle Mis Pólizas
        function togglePolicies() {
            const content = document.getElementById('policiesContent');
            const toggle = document.getElementById('policiesToggle');
            
            content.classList.toggle('collapsed');
            toggle.classList.toggle('rotated');
        }

        // Toggle Sidebar Accesos Directos
        function toggleSidebar() {
            const content = document.getElementById('sidebarContent');
            const toggle = document.getElementById('sidebarToggle');
            
            content.classList.toggle('collapsed');
            toggle.classList.toggle('rotated');
        }

        // Toggle Policy Details
        function toggleDetails(id) {
            const details = document.getElementById(`details-${id}`);
            
            // Cerrar otros detalles abiertos
            document.querySelectorAll('.policy-details-expanded').forEach(detail => {
                if (detail.id !== `details-${id}`) {
                    detail.style.display = 'none';
                }
            });
            
            // Toggle el seleccionado
            if (details.style.display === 'block') {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
            }
        }
    </script>
</body>
</html>