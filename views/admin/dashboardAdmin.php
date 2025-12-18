<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Henríquez y Asociados</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Section */
        .sidebar-header {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .company-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        
        .system-name {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 1.5rem;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .logo-icon {
            font-size: 2.5rem;
        }
        
        .admin-info {
            margin-top: 1rem;
        }
        
        .admin-name {
            font-size: 1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.25rem;
        }
        
        .admin-role {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }
        
        /* Navigation */
        .nav-section {
            flex: 1;
            padding: 1.5rem 0;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
        }
        
        .nav-icon {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }
        
        /* Logout Section */
        .logout-section {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }
        
        .logout-btn {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #1e3c72;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2a5298;
            box-shadow: 0 4px 12px rgba(30,60,114,0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
        }
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }
        
        .stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.green { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon.purple { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .stat-change {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .stat-change.positive {
            color: #10b981;
        }
        
        /* Card */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
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
        
        /* Search Bar */
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #1e3c72;
        }
        
        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            cursor: pointer;
        }
        
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background: #f8fafc;
        }
        
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .data-table tbody tr {
            transition: background 0.3s ease;
        }
        
        .data-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .client-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .client-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .client-info {
            display: flex;
            flex-direction: column;
        }
        
        .client-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        .client-email {
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-activo {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-pendiente {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-suspendido {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .btn-edit:hover {
            background: #bfdbfe;
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-delete:hover {
            background: #fecaca;
        }
        
        .btn-view {
            background: #f3e8ff;
            color: #6b21a8;
        }
        
        .btn-view:hover {
            background: #e9d5ff;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .close-btn {
            font-size: 1.5rem;
            cursor: pointer;
            border: none;
            background: none;
            color: #64748b;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #475569;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1e3c72;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Header with Company Name, Logo and Admin Info -->
        <div class="sidebar-header">
            <div class="company-name">Henríquez y Asociados</div>
            <div class="system-name">Sistema Administrativo</div>
            
            <div class="logo-container">
                <span class="logo-icon">🛡️</span>
            </div>
            
            <div class="admin-info">
                <div class="admin-name">Admin Henríquez</div>
                <div class="admin-role">Administrador</div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="nav-section">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link active">
                        <span class="nav-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👥</span>
                        <span>Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📋</span>
                        <span>Pólizas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">💰</span>
                        <span>Pagos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">🏢</span>
                        <span>Aseguradoras</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📄</span>
                        <span>Documentos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📈</span>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">🔔</span>
                        <span>Alertas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">⚙️</span>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Logout Button -->
        <div class="logout-section">
            <button class="logout-btn">
                <span>🚪</span>
                <span>Cerrar Sesión</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1 class="page-title">Gestión de Clientes</h1>
            <div class="header-actions">
                <button class="btn btn-secondary">📊 Exportar</button>
                <button class="btn btn-primary" onclick="openModal()">+ Nuevo Cliente</button>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">👥</div>
                <div class="stat-content">
                    <div class="stat-label">Total Clientes</div>
                    <div class="stat-value">248</div>
                    <div class="stat-change positive">↑ 12% este mes</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">✓</div>
                <div class="stat-content">
                    <div class="stat-label">Clientes Activos</div>
                    <div class="stat-value">234</div>
                    <div class="stat-change positive">94.4%</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">⏱</div>
                <div class="stat-content">
                    <div class="stat-label">Pendientes</div>
                    <div class="stat-value">8</div>
                    <div class="stat-change">Requieren aprobación</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">📋</div>
                <div class="stat-content">
                    <div class="stat-label">Pólizas Totales</div>
                    <div class="stat-value">456</div>
                    <div class="stat-change positive">↑ 8% este mes</div>
                </div>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Lista de Clientes</h2>
            </div>

            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Buscar por nombre, email o cédula...">
                <select class="filter-select">
                    <option>Todos los estados</option>
                    <option>Activos</option>
                    <option>Pendientes</option>
                    <option>Suspendidos</option>
                </select>
                <select class="filter-select">
                    <option>Todos los ejecutivos</option>
                    <option>María Alvarado</option>
                    <option>Carlos Pérez</option>
                    <option>Ana González</option>
                </select>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Cédula/RUC</th>
                        <th>Ejecutivo</th>
                        <th>Pólizas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">JD</div>
                                <div class="client-info">
                                    <span class="client-name">Juan Delgado</span>
                                    <span class="client-email">jdelgado@email.com</span>
                                </div>
                            </div>
                        </td>
                        <td>Personal</td>
                        <td>8-123-4567</td>
                        <td>María Alvarado</td>
                        <td><strong>5</strong> pólizas</td>
                        <td><span class="status-badge badge-activo">Activo</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view" title="Ver detalles">👁️</button>
                                <button class="btn-icon btn-edit" title="Editar">✏️</button>
                                <button class="btn-icon btn-delete" title="Desactivar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">MR</div>
                                <div class="client-info">
                                    <span class="client-name">María Rodríguez</span>
                                    <span class="client-email">mrodriguez@email.com</span>
                                </div>
                            </div>
                        </td>
                        <td>Personal</td>
                        <td>9-234-5678</td>
                        <td>Carlos Pérez</td>
                        <td><strong>3</strong> pólizas</td>
                        <td><span class="status-badge badge-activo">Activo</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view">👁️</button>
                                <button class="btn-icon btn-edit">✏️</button>
                                <button class="btn-icon btn-delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">EC</div>
                                <div class="client-info">
                                    <span class="client-name">Empresa Corp S.A.</span>
                                    <span class="client-email">info@empresacorp.com</span>
                                </div>
                            </div>
                        </td>
                        <td>Empresa</td>
                        <td>12345-67-890123</td>
                        <td>Ana González</td>
                        <td><strong>8</strong> pólizas</td>
                        <td><span class="status-badge badge-activo">Activo</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view">👁️</button>
                                <button class="btn-icon btn-edit">✏️</button>
                                <button class="btn-icon btn-delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">LG</div>
                                <div class="client-info">
                                    <span class="client-name">Luis García</span>
                                    <span class="client-email">lgarcia@email.com</span>
                                </div>
                            </div>
                        </td>
                        <td>Personal</td>
                        <td>7-345-6789</td>
                        <td>María Alvarado</td>
                        <td><strong>2</strong> pólizas</td>
                        <td><span class="status-badge badge-pendiente">Pendiente</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view">👁️</button>
                                <button class="btn-icon btn-edit">✏️</button>
                                <button class="btn-icon btn-delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar">CM</div>
                                <div class="client-info">
                                    <span class="client-name">Carmen Morales</span>
                                    <span class="client-email">cmorales@email.com</span>
                                </div>
                            </div>
                        </td>
                        <td>Personal</td>
                        <td>8-456-7890</td>
                        <td>Carlos Pérez</td>
                        <td><strong>4</strong> pólizas</td>
                        <td><span class="status-badge badge-activo">Activo</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-view">👁️</button>
                                <button class="btn-icon btn-edit">✏️</button>
                                <button class="btn-icon btn-delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Nuevo Cliente -->
    <div class="modal" id="clientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Nuevo Cliente</h2>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>

            <form>
                <div class="form-group">
                    <label class="form-label">Tipo de Cliente</label>
                    <select class="form-input">
                        <option>Personal</option>
                        <option>Empresa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre Completo / Razón Social</label>
                    <input type="text" class="form-input" placeholder="Ingrese el nombre">
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" placeholder="cliente@email.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Cédula / RUC</label>
                    <input type="text" class="form-input" placeholder="X-XXX-XXXX">
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-input" placeholder="+507 XXXX-XXXX">
                </div>

                <div class="form-group">
                    <label class="form-label">Ejecutivo Asignado</label>
                    <select class="form-input">
                        <option>María Alvarado</option>
                        <option>Carlos Pérez</option>
                        <option>Ana González</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <textarea class="form-input" rows="3" placeholder="Dirección completa"></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('clientModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('clientModal').classList.remove('active');
        }

        document.getElementById('clientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelector('.search-input').addEventListener('input', function() {
            console.log('Buscando:', this.value);
        });
    </script>
</body>
</html>