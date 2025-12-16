<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Póliza - Henríquez y Asociados</title>
    <link rel="stylesheet" href="http://localhost/web_polizas/public/css/cliente/poliza-detalle.css">
    <link rel="stylesheet" href="styles.css">    
</head>
<body>
    <div class="header">
        <div class="breadcrumb">
            <a href="/">Inicio</a> / <a href="/polizas">Mis Pólizas</a> / Detalle
        </div>
        <h1 class="page-title">Detalle de Póliza</h1>
    </div>

    <div class="container">
        <a href="dashboardCliente.php" class="back-button">
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
                <!-- Coberturas Tab -->
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
                        <button class="btn btn-primary">Descargar Historial</button>
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
                        <button class="btn" style="background: #f8fafc; border: 1px solid #e2e8f0;">📄 Solicitar Certificado</button>
                        <button class="btn" style="background: #f8fafc; border: 1px solid #e2e8f0;">🔄 Renovar Póliza</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                // Aquí iría la lógica de descarga
            });
        });
    </script>
</body>
</html>