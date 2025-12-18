<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - Henríquez & Asociados</title>
    <link rel="stylesheet" href="http://localhost/Gestion_de_Polizas/public/css/cliente/dashboardCliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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