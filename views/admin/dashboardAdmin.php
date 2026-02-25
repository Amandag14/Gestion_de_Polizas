<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Henríquez & Asociados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            height: 100%;
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
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex-shrink: 0;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            border-bottom-color: rgba(255,255,255,0.5);
        }

        .nav-item.active {
            border-bottom-color: white;
            background: rgba(255,255,255,0.1);
        }

        /* Main Content */
        .main-content {
            flex: 1 0 auto;
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e3c72;
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
            background: #004B93;
            color: white;
        }

        .btn-primary:hover {
            background: #0066B3;
            box-shadow: 0 4px 12px rgba(0,75,147,0.3);
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
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.75rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border-left: 4px solid #3b82f6;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            overflow: hidden;
        }

        .stat-card:nth-child(1) {
            border-left-color: #3b82f6;
        }

        .stat-card:nth-child(2) {
            border-left-color: #10b981;
        }

        .stat-card:nth-child(3) {
            border-left-color: #f59e0b;
        }

        .stat-card:nth-child(4) {
            border-left-color: #8b5cf6;
        }

        .stat-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            min-width: 0;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            line-height: 1.3;
        }

        .stat-subtitle {
            font-size: 0.8rem;
            color: #94a3b8;
            line-height: 1.4;
        }

        .stat-value {
            font-size: clamp(1.5rem, 3.5vw, 2rem);
            font-weight: 700;
            color: #1e293b;
            text-align: right;
            flex-shrink: 0;
            line-height: 1;
            white-space: nowrap;
        }

        /* Card */
        .card {
            background: white;
            border-radius: 8px;
            border: 1px solid #d9e1ed;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background: #f3f6fb;
            padding: 12px 20px;
            border-bottom: 1px solid #d9e1ed;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1a2e57;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            gap: 1rem;
            padding: 1.5rem 1.5rem 0;
            background: white;
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
            border-color: #004B93;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 0.9rem;
        }

        /* Table */
        .table-container {
            padding: 1.5rem;
            background: white;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #2f5597;
            color: white;
        }

        .data-table th {
            padding: 12px 20px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
        }

        .data-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #e7ecf4;
            font-size: 14px;
        }

        .data-table tbody tr {
            transition: background 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: #f5f8fd;
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
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .client-info {
            display: flex;
            flex-direction: column;
        }

        .client-name {
            font-weight: 600;
            color: #003366;
            font-size: 14px;
        }

        .client-email {
            font-size: 13px;
            color: #7a8aa7;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-activo {
            background: #dff5e3;
            color: #237a3b;
        }

        .badge-pendiente {
            background: #fff3cd;
            color: #946c00;
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
            font-size: 0.9rem;
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

        /* Footer */
        .footer {
            flex-shrink: 0;
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 1.5rem 2rem;
            margin-top: auto;
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

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a2e57;
        }

        .close-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #64748b;
            transition: all 0.3s;
        }

        .close-btn:hover {
            background: #dc2626;
            color: white;
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #475569;
            font-size: 0.9rem;
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
            border-color: #004B93;
            box-shadow: 0 0 0 3px rgba(0, 75, 147, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.25rem 1rem;
            }
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .search-bar {
                flex-direction: column;
            }

            .data-table {
                font-size: 0.85rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
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
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    Inicio
                </a>
                <a href="#" class="nav-item active">
                    <i class="fas fa-users"></i>
                    Clientes
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-file-contract"></i>
                    Pólizas
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-folder"></i>
                    Documentos
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar"></i>
                    Reportes
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i>
                    Configuración
                </a>
            </div>
            <div class="user-info">
                <div class="user-name">ADMIN HENRÍQUEZ</div>
                <div class="user-time">Administrador</div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Gestión de Clientes</h1>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i>
                    Nuevo Cliente
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-label">TOTAL CLIENTES</div>
                    <div class="stat-subtitle">Registrados en el sistema</div>
                </div>
                <div class="stat-value">248</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-label">CLIENTES ACTIVOS</div>
                    <div class="stat-subtitle">Con pólizas vigentes</div>
                </div>
                <div class="stat-value">234</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-label">PENDIENTES</div>
                    <div class="stat-subtitle">Requieren aprobación</div>
                </div>
                <div class="stat-value">8</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-label">PÓLIZAS TOTALES</div>
                    <div class="stat-subtitle">Todas las pólizas</div>
                </div>
                <div class="stat-value">456</div>
            </div>
        </div>

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

            <div class="table-container">
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
                                    <button class="btn-icon btn-view" title="Ver detalles"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon btn-delete" title="Desactivar"><i class="fas fa-trash"></i></button>
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
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
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
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
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
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
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
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn-icon btn-delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
                        <option>Rosa Peña</option>
                        <option>Jair Nuñez</option>
                        <option>Ana Julia</option>
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
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('clientModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('clientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        document.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-item').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelector('.search-input').addEventListener('input', function() {
            console.log('Buscando:', this.value);
        });

        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filtro aplicado:', this.value);
            });
        });
    </script>
</body>
</html>