<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Póliza - Henríquez y Asociados</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
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
            gap: 0.5rem;
        }

        .header-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
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
        }

        .header-btn span {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .nav-menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .nav-left {
            display: flex;
        }

        .nav-item {
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            border-bottom-color: rgba(255,255,255,0.5);
        }

        .nav-item.active {
            border-bottom-color: white;
            background: rgba(255,255,255,0.1);
        }

        .user-info {
            text-align: right;
            padding: 0.5rem 0;
        }

        .user-name {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-time {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        /* Container - ANCHO COMPLETO */
        .container {
            max-width: 100%;
            width: 100%;
            margin: 0;
            padding: 2rem;
            flex: 1;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #475569;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: #f8fafc;
        }

        /* Hero Section - ANCHO COMPLETO */
        .policy-hero {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            width: 100%;
        }

        .hero-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .hero-info {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .hero-icon {
            font-size: 3rem;
        }

        .hero-details h1 {
            font-size: 1.75rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .hero-meta {
            display: flex;
            gap: 1.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .badge-vigente {
            background: #d1fae5;
            color: #065f46;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Content Grid - ANCHO COMPLETO */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            width: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* Tabs */
        .tab-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #004B93;
        }

        .tab-btn.active {
            color: #004B93;
            border-bottom-color: #004B93;
        }

        /* Coverage List */
        .coverage-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .coverage-item {
            padding: 1.25rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #004B93;
        }

        .coverage-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .coverage-name {
            font-weight: 600;
            color: #1e293b;
        }

        .coverage-amount {
            font-weight: 700;
            color: #004B93;
            font-size: 1.1rem;
        }

        .coverage-desc {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Payment Timeline */
        .payment-timeline {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            width: 2px;
            height: calc(100% + 1rem);
            background: #e2e8f0;
        }

        .timeline-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .timeline-dot.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .timeline-dot.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .timeline-meta {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .timeline-amount {
            font-size: 1.1rem;
            font-weight: 700;
            color: #004B93;
        }

        /* Documents */
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .document-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .document-item:hover {
            background: #f1f5f9;
        }

        .document-icon {
            font-size: 2rem;
        }

        .document-info {
            flex: 1;
        }

        .document-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .document-meta {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Sidebar */
        .sidebar .card:last-child {
            margin-bottom: 0;
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }

        /* Contact Card */
        .contact-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 2rem;
        }

        .contact-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .contact-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .contact-role {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .contact-info {
            text-align: left;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .contact-info div {
            padding: 0.5rem 0;
            color: #475569;
            font-size: 0.9rem;
        }

        .contact-btn {
            width: 100%;
            padding: 0.75rem;
            background: #004B93;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .contact-btn:hover {
            background: #003770;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #004B93;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background: #003770;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 1.5rem 2rem;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left span {
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

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .hero-header {
                flex-direction: column;
                gap: 1rem;
            }

            .hero-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .header-top {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-left {
                width: 100%;
                flex-wrap: wrap;
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

            .tab-nav {
                flex-wrap: wrap;
            }

            .tab-btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
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
                <a href="http://localhost/Gestion_de_Polizas/views/cliente/dashboardCliente.php" class="nav-item active">Pólizas</a>
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
        <a href="http://localhost/Gestion_de_Polizas/views/cliente/dashboardCliente.php" class="back-button">
            ← Volver al Inicio
        </a>

        <!-- Hero Section -->
        <div class="policy-hero">
            <div class="hero-header">
                <div class="hero-info">
                    <div class="hero-icon">❤️</div>
                    <div class="hero-details">
                        <h1>Seguro de Vida Individual</h1>
                        <div class="hero-meta">
                            <span>📋 VID-2024-001234</span>
                            <span>🏢 ASSA Seguros</span>
                        </div>
                    </div>
                </div>
                <span class="status-badge badge-vigente">✓ Vigente</span>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-label">Prima Total</div>
                    <div class="stat-value">$1,200</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Suma Asegurada</div>
                    <div class="stat-value">$100,000</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Días Restantes</div>
                    <div class="stat-value">248</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Estado Pago</div>
                    <div class="stat-value" style="color: #10b981;">Al Día</div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-content">
                <!-- Coberturas -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Información de la Póliza</h2>
                    </div>

                    <div class="tab-nav">
                        <button class="tab-btn active">Coberturas</button>
                        <button class="tab-btn">Beneficiarios</button>
                        <button class="tab-btn">Renovaciones</button>
                    </div>

                    <div class="coverage-list">
                        <div class="coverage-item">
                            <div class="coverage-header">
                                <span class="coverage-name">Muerte Natural</span>
                                <span class="coverage-amount">$100,000</span>
                            </div>
                            <div class="coverage-desc">
                                Cobertura por fallecimiento por causas naturales. Incluye indemnización completa a los beneficiarios designados.
                            </div>
                        </div>

                        <div class="coverage-item">
                            <div class="coverage-header">
                                <span class="coverage-name">Muerte Accidental</span>
                                <span class="coverage-amount">$200,000</span>
                            </div>
                            <div class="coverage-desc">
                                Doble indemnización en caso de muerte por accidente. Cobertura 24/7 en todo el territorio nacional.
                            </div>
                        </div>

                        <div class="coverage-item">
                            <div class="coverage-header">
                                <span class="coverage-name">Invalidez Total y Permanente</span>
                                <span class="coverage-amount">$50,000</span>
                            </div>
                            <div class="coverage-desc">
                                Pago adelantado del 50% de la suma asegurada en caso de invalidez total y permanente certificada.
                            </div>
                        </div>

                        <div class="coverage-item">
                            <div class="coverage-header">
                                <span class="coverage-name">Enfermedades Graves</span>
                                <span class="coverage-amount">$30,000</span>
                            </div>
                            <div class="coverage-desc">
                                Cobertura por diagnóstico de enfermedades como cáncer, infarto, ACV. Incluye 10 enfermedades graves.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Pagos -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Historial de Pagos</h2>
                        <button class="btn btn-primary" style="width: auto;">Descargar Historial</button>
                    </div>

                    <div class="payment-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot paid">✓</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Pago Anual 2024</div>
                                <div class="timeline-meta">
                                    Pagado el 10 de Enero, 2024 • Transferencia Bancaria
                                </div>
                                <div class="timeline-amount">$1,200.00</div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot paid">✓</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Pago Anual 2023</div>
                                <div class="timeline-meta">
                                    Pagado el 10 de Enero, 2023 • Cheque
                                </div>
                                <div class="timeline-amount">$1,150.00</div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot paid">✓</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Pago Anual 2022</div>
                                <div class="timeline-meta">
                                    Pagado el 10 de Enero, 2022 • Transferencia Bancaria
                                </div>
                                <div class="timeline-amount">$1,100.00</div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot pending">⏱</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Próximo Pago 2025</div>
                                <div class="timeline-meta">
                                    Vence el 10 de Enero, 2025
                                </div>
                                <div class="timeline-amount">$1,200.00</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentos -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Documentos</h2>
                    </div>

                    <div class="document-list">
                        <div class="document-item">
                            <div class="document-icon">📄</div>
                            <div class="document-info">
                                <div class="document-name">Póliza Original VID-2024-001234.pdf</div>
                                <div class="document-meta">Subido el 10/01/2024 • 2.3 MB</div>
                            </div>
                        </div>

                        <div class="document-item">
                            <div class="document-icon">📄</div>
                            <div class="document-info">
                                <div class="document-name">Certificado de Cobertura.pdf</div>
                                <div class="document-meta">Subido el 10/01/2024 • 1.1 MB</div>
                            </div>
                        </div>

                        <div class="document-item">
                            <div class="document-icon">📄</div>
                            <div class="document-info">
                                <div class="document-name">Recibo de Pago 2024.pdf</div>
                                <div class="document-meta">Subido el 10/01/2024 • 450 KB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Información General -->
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: 1.5rem;">Información General</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Fecha de Inicio</span>
                            <span class="info-value">10 de Enero, 2024</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Vencimiento</span>
                            <span class="info-value">10 de Enero, 2025</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Forma de Pago</span>
                            <span class="info-value">Anual</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Deducible</span>
                            <span class="info-value">N/A</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Renovación Automática</span>
                            <span class="info-value">✓ Activada</span>
                        </div>
                    </div>
                </div>

                <!-- Ejecutivo de Cuenta -->
                <div class="contact-card">
                    <div class="contact-avatar">MA</div>
                    <div class="contact-name">María Alvarado</div>
                    <div class="contact-role">Tu Ejecutiva de Cuenta</div>
                    <div class="contact-info">
                        <div>📧 malvarado@henriquez.com</div>
                        <div>📱 +507 6234-5678</div>
                        <div>🕐 Lun-Vie 8:00 AM - 5:00 PM</div>
                    </div>
                    <button class="contact-btn">Enviar Mensaje</button>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: 1rem;">Acciones Rápidas</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <button class="btn btn-primary">💳 Realizar Pago</button>
                        <button class="btn" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;">📄 Solicitar Certificado</button>
                        <button class="btn" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;">🔄 Renovar Póliza</button>
                    </div>
                </div>
            </div>
        </div>
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
        // Tabs functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                console.log('Tab activa:', this.textContent);
            });
        });

        // Document download
        document.querySelectorAll('.document-item').forEach(item => {
            item.addEventListener('click', function() {
                const docName = this.querySelector('.document-name').textContent;
                console.log('Descargar:', docName);
                alert('Descargando: ' + docName);
                // Aquí iría la lógica de descarga real
            });
        });

        // Quick action buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!this.textContent.includes('Descargar')) {
                    console.log('Acción:', this.textContent.trim());
                }
            });
        });

        // Contact button
        document.querySelector('.contact-btn')?.addEventListener('click', function() {
            alert('Enviando mensaje a María Alvarado...');
        });
    </script>
</body>
</html>