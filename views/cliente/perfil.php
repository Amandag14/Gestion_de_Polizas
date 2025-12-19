<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Henríquez y Asociados</title>
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

        /* Alert Messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Page Header - ANCHO COMPLETO */
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 2rem;
            width: 100%;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .profile-header-info h1 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .profile-meta {
            display: flex;
            gap: 2rem;
            color: #64748b;
            font-size: 0.95rem;
        }

        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Cards - ANCHO COMPLETO */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            width: 100%;
        }

        .card.full-width {
            grid-column: 1 / -1;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-toggle-btn {
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border: none;
            border-radius: 6px;
            color: #475569;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .edit-toggle-btn:hover {
            background: #e2e8f0;
        }

        .edit-toggle-btn.active {
            background: #004B93;
            color: white;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            width: 100%;
        }

        .stat-box {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .stat-box-value {
            font-size: 2rem;
            font-weight: 700;
            color: #004B93;
            margin-bottom: 0.25rem;
        }

        .stat-box-label {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Content Grid - ANCHO COMPLETO */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            width: 100%;
        }

        /* Info Display */
        .info-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
            font-weight: 500;
        }

        /* Form */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #004B93;
            box-shadow: 0 0 0 3px rgba(0, 75, 147, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-input.readonly {
            background: #f8fafc;
            color: #64748b;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
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
        }

        .btn-primary:hover {
            background: #003770;
        }

        .btn-outline {
            background: white;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        .btn-outline:hover {
            background: #f8fafc;
        }

        /* Security Section */
        .security-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .security-info h4 {
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .security-info p {
            font-size: 0.85rem;
            color: #64748b;
        }

        .btn-change {
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #004B93;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-change:hover {
            background: #004B93;
            color: white;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            color: #1e293b;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
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

        /* Hidden class */
        .hidden {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .info-display {
                grid-template-columns: 1fr;
            }

            .form-grid {
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
                <a href="http://localhost/Gestion_de_Polizas/views/cliente/dashboardCliente.php" class="nav-item">Pólizas</a>
                <a href="#" class="nav-item">Consultas</a>
                <a href="#" class="nav-item">Transacciones</a>
                <a href="#" class="nav-item">Recargas</a>
                <a href="#" class="nav-item">Solicitudes</a>
                <a href="http://localhost/Gestion_de_Polizas/views/cliente/perfil.php" class="nav-item active">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, JUAN DELGADO</div>
                <div class="user-time">Último ingreso hace 14 minutos</div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <a href="http://localhost/Gestion_de_Polizas/views/cliente/dashboardCliente.php" class="back-button">← Volver al Inicio</a>

        <!-- Success Alert -->
        <div class="alert alert-success hidden" id="successAlert">
            ✓ Perfil actualizado correctamente
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div class="profile-avatar-large">JD</div>
            <div class="profile-header-info">
                <h1>Juan Carlos Delgado Martínez</h1>
                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <span>📧</span>
                        <span>jdelgado@email.com</span>
                    </div>
                    <div class="profile-meta-item">
                        <span>📱</span>
                        <span>+507 6234-5678</span>
                    </div>
                    <div class="profile-meta-item">
                        <span>📅</span>
                        <span>Cliente desde Ene 2020</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="card">
            <h3 class="card-title">Resumen de Cuenta</h3>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-box-value">5</div>
                    <div class="stat-box-label">Pólizas Activas</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">4</div>
                    <div class="stat-box-label">Años como Cliente</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-value">$4,250</div>
                    <div class="stat-box-label">Prima Anual Total</div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Personal Information -->
            <div style="grid-column: 1 / -1;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">👤 Información Personal</h3>
                        <button class="edit-toggle-btn" onclick="toggleEditMode('personal')">
                            Editar
                        </button>
                    </div>

                    <!-- Display Mode -->
                    <div class="info-display" id="personalDisplay">
                        <div class="info-item">
                            <span class="info-label">Nombre Completo</span>
                            <span class="info-value">Juan Carlos Delgado Martínez</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tipo de Cliente</span>
                            <span class="info-value">Personal</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Cédula</span>
                            <span class="info-value">8-123-4567</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Nacimiento</span>
                            <span class="info-value">15 de Marzo, 1985</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">jdelgado@email.com</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Teléfono</span>
                            <span class="info-value">+507 6234-5678</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Celular</span>
                            <span class="info-value">+507 6789-1234</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Provincia</span>
                            <span class="info-value">Panamá</span>
                        </div>
                        <div class="info-item full-width">
                            <span class="info-label">Dirección</span>
                            <span class="info-value">Calle 50, Edificio Torre Global, Piso 12, Oficina 1205</span>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <form class="form-grid hidden" id="personalForm">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-input" value="Juan Carlos" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-input" value="Delgado Martínez" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cédula</label>
                            <input type="text" class="form-input readonly" value="8-123-4567" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-input" value="1985-03-15">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="jdelgado@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-input" value="+507 6234-5678">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Celular</label>
                            <input type="tel" class="form-input" value="+507 6789-1234">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Provincia</label>
                            <select class="form-select">
                                <option value="panama" selected>Panamá</option>
                                <option value="colon">Colón</option>
                                <option value="chiriqui">Chiriquí</option>
                                <option value="bocas">Bocas del Toro</option>
                                <option value="veraguas">Veraguas</option>
                                <option value="cocle">Coclé</option>
                                <option value="herrera">Herrera</option>
                                <option value="los_santos">Los Santos</option>
                                <option value="darien">Darién</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Dirección Completa</label>
                            <textarea class="form-textarea">Calle 50, Edificio Torre Global, Piso 12, Oficina 1205</textarea>
                        </div>
                        <div class="form-actions full-width">
                            <button type="button" class="btn btn-outline" onclick="toggleEditMode('personal')">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ejecutivo Asignado -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tu Ejecutivo de Cuenta</h3>
                </div>
                <div class="security-item" style="flex-direction: column; align-items: flex-start; background: linear-gradient(135deg, #004B93 0%, #0066B3 100%); color: white;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; width: 100%;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                            MA
                        </div>
                        <div>
                            <h4 style="color: white;">María Alvarado Ramos</h4>
                            <p style="color: rgba(255,255,255,0.9);">Ejecutiva Senior</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>📧</span>
                            <span>malvarado@henriquez.com</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>📱</span>
                            <span>+507 6345-6789</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>🕐</span>
                            <span>Lun-Vie 8:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                    <button class="btn" style="width: 100%; margin-top: 1rem; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                        📨 Enviar Mensaje
                    </button>
                </div>
            </div>

            <!-- Seguridad -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🔒 Seguridad de la Cuenta</h3>
                </div>
                
                <div class="security-item">
                    <div class="security-info">
                        <h4>Contraseña</h4>
                        <p>Última actualización: hace 3 meses</p>
                    </div>
                    <button class="btn-change" onclick="showPasswordModal()">
                        Cambiar
                    </button>
                </div>

                <div class="security-item">
                    <div class="security-info">
                        <h4>Autenticación de Dos Factores</h4>
                        <p>No configurado</p>
                    </div>
                    <button class="btn-change" onclick="show2FAModal()">
                        Activar
                    </button>
                </div>

                <div class="security-item">
                    <div class="security-info">
                        <h4>Sesiones Activas</h4>
                        <p>2 dispositivos conectados</p>
                    </div>
                    <button class="btn-change" onclick="showSessionsModal()">
                        Ver Sesiones
                    </button>
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

    <!-- Modales -->
    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cambiar Contraseña</h3>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-input" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('passwordModal')">Cancelar</button>
                <button class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>

    <script>
        // Toggle edit mode
        function toggleEditMode(section) {
            const display = document.getElementById(section + 'Display');
            const form = document.getElementById(section + 'Form');
            const btn = document.querySelector(`[onclick="toggleEditMode('${section}')"]`);
            
            if (display.classList.contains('hidden')) {
                display.classList.remove('hidden');
                form.classList.add('hidden');
                btn.textContent = 'Editar';
                btn.classList.remove('active');
            } else {
                display.classList.add('hidden');
                form.classList.remove('hidden');
                btn.textContent = 'Cancelar';
                btn.classList.add('active');
            }
        }

        // Form submission
        document.getElementById('personalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            toggleEditMode('personal');
            showAlert('successAlert', 'Perfil actualizado correctamente');
        });

        // Modal functions
        function showPasswordModal() {
            document.getElementById('passwordModal').classList.add('active');
        }

        function show2FAModal() {
            alert('Funcionalidad de 2FA en desarrollo');
        }

        function showSessionsModal() {
            alert('Funcionalidad de sesiones en desarrollo');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Alert function
        function showAlert(alertId, message) {
            const alert = document.getElementById(alertId);
            alert.textContent = '✓ ' + message;
            alert.classList.remove('hidden');
            
            setTimeout(() => {
                alert.classList.add('hidden');
            }, 3000);
        }

        // Close modal on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>