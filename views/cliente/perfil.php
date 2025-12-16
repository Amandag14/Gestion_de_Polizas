<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Henríquez y Asociados</title>
    <link rel="stylesheet" href="http://localhost/web_polizas/public/css/cliente/perfil.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-top">
            <div class="logo">
                Henríquez & Asociados
                <span class="logo-badge">Asesores de seguros</span>
            </div>
            <div class="header-actions">
                <div class="header-icon" title="Chat">💬</div>
                <div class="header-icon" title="Ayuda">❓</div>
                <div class="header-icon" title="Mensajes">✉️</div>
                <div class="header-icon" title="Salir">🚪</div>
                <div class="user-info">
                    <div class="user-name">Hola, JUAN DELGADO</div>
                    <div class="user-time">Último ingreso hace 14 minutos</div>
                </div>
            </div>
        </div>
        <nav class="nav-menu">
            <a class="nav-item" href="#">Pólizas</a>
            <a class="nav-item" href="#">Consultas</a>
            <a class="nav-item" href="#">Transacciones</a>
            <a class="nav-item" href="#">Recargas</a>
            <a class="nav-item" href="#">Solicitudes</a>
            <a class="nav-item active" href="#">Configuraciones</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="container">
        <a href="dashboardCliente.php" class="back-button">← Volver al Dashboard</a>

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
            <div class="card full-width">
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
                    <h3 class="card-title">Seguridad de la Cuenta</h3>
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
    <div class="footer">
        <div class="footer-left">
            Copyright © 2025 Henríquez & Asociados. Todos los derechos reservados.
        </div>
        <div class="footer-links">
            <a href="#" class="footer-link">Seguridad</a>
            <a href="#" class="footer-link">Privacidad</a>
            <a href="#" class="footer-link">Salir</a>
        </div>
    </div>

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