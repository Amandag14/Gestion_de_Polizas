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
    <title>Reclamaciones - Henríquez & Asociados</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-title-section {
            flex: 1;
        }

        .page-title {
            font-size: 32px;
            color: #0d5ba8;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        .btn-new-claim {
            background: #1976d2;
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-new-claim:hover {
            background: #0d5ba8;
            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .close {
            color: white;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            line-height: 1;
        }

        .close:hover {
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 0 3px rgba(25,118,210,0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .file-upload {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload:hover {
            border-color: #1976d2;
            background: #f5f9ff;
        }

        .file-upload i {
            font-size: 48px;
            color: #1976d2;
            margin-bottom: 15px;
        }

        .file-upload input {
            display: none;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-submit,
        .btn-cancel {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit {
            background: #1976d2;
            color: white;
        }

        .btn-submit:hover {
            background: #0d5ba8;
        }

        .btn-cancel {
            background: #f5f5f5;
            color: #666;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 40px;
        }

        .quick-actions-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
            padding: 25px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            border-color: #1976d2;
            background: white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .action-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .action-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .action-desc {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .claims-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 40px;
        }

        .claim-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .claim-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
        }

        .claim-card.en-revision::before {
            background: #ff9800;
        }

        .claim-card.aprobado::before {
            background: #4caf50;
        }

        .claim-card.pagado::before {
            background: #2196f3;
        }

        .claim-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .claim-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .claim-info {
            flex: 1;
        }

        .claim-number {
            font-size: 14px;
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .claim-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .claim-poliza {
            font-size: 14px;
            color: #666;
        }

        .claim-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-revision {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-aprobado {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-pagado {
            background: #e3f2fd;
            color: #1565c0;
        }

        .claim-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .claim-detail {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-size: 13px;
            color: #999;
            font-weight: 500;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            font-weight: 600;
        }

        .claim-actions {
            display: flex;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #1976d2;
            color: white;
        }

        .btn-primary:hover {
            background: #0d5ba8;
        }

        .btn-secondary {
            background: #f5f7fa;
            color: #1976d2;
            border: 1px solid #1976d2;
        }

        .btn-secondary:hover {
            background: #1976d2;
            color: white;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
            border-radius: 10px;
            transition: width 0.3s ease;
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-new-claim {
                width: 100%;
                justify-content: center;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
            }

            .claim-body {
                grid-template-columns: 1fr;
            }

            .claim-actions {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
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
                <a href="dashboardCliente.php" class="nav-item">Pólizas</a>
                <a href="documentos.php" class="nav-item">Documentos</a>
                <a href="reclamos.php" class="nav-item active">Reclamos</a>
                <a href="perfil.php" class="nav-item">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, <?php echo htmlspecialchars(strtoupper($user_name)); ?></div>
                <div class="user-time"><?php echo htmlspecialchars($ultimo_ingreso_texto); ?></div>
            </div>
        </nav>
    </header>
    

    <div class="container">
        <div class="page-header">
            <div class="page-title-section">
                <h1 class="page-title">Mis Reclamos</h1>
                <p class="page-subtitle">Gestiona y da seguimiento a tus reclamaciones de manera fácil</p>
            </div>
            <button class="btn-new-claim" onclick="openModal()">
                <i class="fas fa-plus"></i>
                Nuevo Reclamo
            </button>
        </div>

        <!-- Modal de Nueva Reclamación -->
        <div id="claimModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-file-medical"></i> Nuevo Reclamo</h2>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="claimForm" onsubmit="submitClaim(event)">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="poliza">Póliza <span style="color: red;">*</span></label>
                                <select id="poliza" name="poliza" required>
                                    <option value="">Seleccione una póliza</option>
                                    <option value="AUTO-2024-00456">AUTO-2024-00456 - Seguro de Auto</option>
                                    <option value="VIDA-2024-00789">VIDA-2024-00789 - Seguro de Vida</option>
                                    <option value="HOGAR-2024-00321">HOGAR-2024-00321 - Seguro de Hogar</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tipo_siniestro">Tipo de Siniestro <span style="color: red;">*</span></label>
                                <select id="tipo_siniestro" name="tipo_siniestro" required>
                                    <option value="">Seleccione el tipo</option>
                                    <option value="Accidente de Tránsito">Accidente de Tránsito</option>
                                    <option value="Robo Total">Robo Total</option>
                                    <option value="Robo Parcial">Robo Parcial</option>
                                    <option value="Daños por Colisión">Daños por Colisión</option>
                                    <option value="Daños por Incendio">Daños por Incendio</option>
                                    <option value="Daños por Inundación">Daños por Inundación</option>
                                    <option value="Cristales">Cristales</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_siniestro">Fecha del Siniestro <span style="color: red;">*</span></label>
                                <input type="date" id="fecha_siniestro" name="fecha_siniestro" required max="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label for="monto_reclamado">Monto Estimado <span style="color: red;">*</span></label>
                                <input type="number" id="monto_reclamado" name="monto_reclamado" placeholder="$0.00" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lugar_siniestro">Lugar del Siniestro <span style="color: red;">*</span></label>
                            <input type="text" id="lugar_siniestro" name="lugar_siniestro" placeholder="Ej: Av. Balboa, Ciudad de Panamá" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción Detallada <span style="color: red;">*</span></label>
                            <textarea id="descripcion" name="descripcion" placeholder="Describa lo sucedido con el mayor detalle posible..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="testigos">¿Hubo Testigos?</label>
                            <input type="text" id="testigos" name="testigos" placeholder="Nombres y teléfonos de testigos (opcional)">
                        </div>

                        <div class="form-group">
                            <label>Documentos Adjuntos</label>
                            <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Haga clic para subir archivos</strong></p>
                                <p style="font-size: 13px; color: #999; margin-top: 10px;">
                                    Fotos del siniestro, reporte policial, facturas, etc.<br>
                                    (Máximo 10MB por archivo)
                                </p>
                                <input type="file" id="fileInput" multiple accept="image/*,.pdf,.doc,.docx">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" form="claimForm" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Reclamo
                    </button>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <div class="quick-actions-title">¿Qué necesitas hacer?</div>
            <div class="actions-grid">
                <div class="action-card" onclick="quickAction('emergencia')">
                    <div class="action-icon"><i class="fas fa-ambulance"></i></div>
                    <div class="action-title">Emergencia 24/7</div>
                    <div class="action-desc">Asistencia inmediata para situaciones urgentes</div>
                </div>

                <div class="action-card" onclick="quickAction('documentos')">
                    <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="action-title">Documentos Requeridos</div>
                    <div class="action-desc">Consulta qué documentos necesitas presentar</div>
                </div>

                <div class="action-card" onclick="quickAction('guia')">
                    <div class="action-icon"><i class="fas fa-book-open"></i></div>
                    <div class="action-title">Guía de Reclamación</div>
                    <div class="action-desc">Aprende cómo hacer una reclamación paso a paso</div>
                </div>

                <div class="action-card" onclick="quickAction('contacto')">
                    <div class="action-icon"><i class="fas fa-comments"></i></div>
                    <div class="action-title">Chat con Asesor</div>
                    <div class="action-desc">Habla directamente con tu asesor de seguros</div>
                </div>
            </div>
        </div>

        <div class="section-title">
            <i class="fas fa-clipboard-list"></i>
            Reclamos Activos
        </div>

        <div class="claims-list">
            <!-- Reclamación en Revisión -->
            <div class="claim-card en-revision">
                <div class="claim-header">
                    <div class="claim-info">
                        <div class="claim-number">RECL-2024-00123</div>
                        <div class="claim-title">Accidente de Tránsito</div>
                        <div class="claim-poliza">Póliza: AUTO-2024-00456</div>
                    </div>
                    <div class="claim-status status-revision">En Revisión</div>
                </div>

                <div class="claim-body">
                    <div class="claim-detail">
                        <div class="detail-label">Fecha del Siniestro</div>
                        <div class="detail-value">15 de Diciembre, 2024</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Monto Reclamado</div>
                        <div class="detail-value">$3,500.00</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Ajustador Asignado</div>
                        <div class="detail-value">Carlos Méndez</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Días en Proceso</div>
                        <div class="detail-value">8 días</div>
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Progreso: 60%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 60%"></div>
                    </div>
                </div>

                <div class="claim-actions">
                    <button class="btn-action btn-primary" onclick="viewClaim('RECL-2024-00123')">
                        <i class="far fa-eye"></i> Ver Detalles
                    </button>
                    <button class="btn-action btn-secondary" onclick="uploadDocs('RECL-2024-00123')">
                        <i class="fas fa-upload"></i> Subir Documentos
                    </button>
                    <button class="btn-action btn-secondary" onclick="contactAdjuster('Carlos Méndez')">
                        <i class="fas fa-phone"></i> Contactar Ajustador
                    </button>
                </div>
            </div>

            <!-- Reclamación Aprobada -->
            <div class="claim-card aprobado">
                <div class="claim-header">
                    <div class="claim-info">
                        <div class="claim-number">RECL-2024-00098</div>
                        <div class="claim-title">Robo de Vehículo - Parcial</div>
                        <div class="claim-poliza">Póliza: AUTO-2024-00456</div>
                    </div>
                    <div class="claim-status status-aprobado">Aprobado</div>
                </div>

                <div class="claim-body">
                    <div class="claim-detail">
                        <div class="detail-label">Fecha del Siniestro</div>
                        <div class="detail-value">28 de Noviembre, 2024</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Monto Aprobado</div>
                        <div class="detail-value">$1,850.00</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Forma de Pago</div>
                        <div class="detail-value">Transferencia Bancaria</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Fecha Estimada de Pago</div>
                        <div class="detail-value">10 de Enero, 2025</div>
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Progreso: 90%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 90%"></div>
                    </div>
                </div>

                <div class="claim-actions">
                    <button class="btn-action btn-primary" onclick="viewClaim('RECL-2024-00098')">
                        <i class="far fa-eye"></i> Ver Detalles
                    </button>
                    <button class="btn-action btn-secondary" onclick="downloadApproval('RECL-2024-00098')">
                        <i class="fas fa-download"></i> Descargar Aprobación
                    </button>
                </div>
            </div>
        </div>

        <div class="section-title">
            <i class="fas fa-check-circle"></i>
            Historial de Reclamaciones
        </div>

        <div class="claims-list">
            <!-- Reclamación Pagada -->
            <div class="claim-card pagado">
                <div class="claim-header">
                    <div class="claim-info">
                        <div class="claim-number">RECL-2024-00045</div>
                        <div class="claim-title">Daños por Colisión</div>
                        <div class="claim-poliza">Póliza: AUTO-2024-00456</div>
                    </div>
                    <div class="claim-status status-pagado">Pagado</div>
                </div>

                <div class="claim-body">
                    <div class="claim-detail">
                        <div class="detail-label">Fecha del Siniestro</div>
                        <div class="detail-value">05 de Septiembre, 2024</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Monto Pagado</div>
                        <div class="detail-value">$2,300.00</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Fecha de Pago</div>
                        <div class="detail-value">20 de Octubre, 2024</div>
                    </div>
                    <div class="claim-detail">
                        <div class="detail-label">Tiempo de Resolución</div>
                        <div class="detail-value">45 días</div>
                    </div>
                </div>

                <div class="claim-actions">
                    <button class="btn-action btn-primary" onclick="viewClaim('RECL-2024-00045')">
                        <i class="far fa-eye"></i> Ver Detalles
                    </button>
                    <button class="btn-action btn-secondary" onclick="downloadReceipt('RECL-2024-00045')">
                        <i class="fas fa-receipt"></i> Comprobante de Pago
                    </button>
                </div>
            </div>
        </div>
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
        // Abrir modal
        function openModal() {
            document.getElementById('claimModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Cerrar modal
        function closeModal() {
            document.getElementById('claimModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('claimForm').reset();
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('claimModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Enviar formulario
        function submitClaim(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            // Aquí iría la lógica para enviar al servidor
            console.log('Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Simulación de envío exitoso
            alert('✅ Reclamación enviada exitosamente!\n\nNúmero de reclamación: RECL-2025-' + Math.floor(Math.random() * 1000).toString().padStart(5, '0') + '\n\nRecibirás una notificación en tu correo con los detalles.');
            
            closeModal();
        }

        // Mostrar archivos seleccionados
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                let fileNames = '';
                for (let i = 0; i < files.length; i++) {
                    fileNames += files[i].name + '\n';
                }
                alert('Archivos seleccionados:\n' + fileNames);
            }
        });

        function quickAction(action) {
            const actions = {
                'emergencia': '📞 Llamando a línea de emergencia 24/7...',
                'documentos': '📄 Abriendo lista de documentos requeridos...',
                'guia': '📖 Mostrando guía paso a paso...',
                'contacto': '💬 Conectando con tu asesor...'
            };
            alert(actions[action] || 'Acción no disponible');
        }

        function viewClaim(claimId) {
            alert('Abriendo detalles completos de: ' + claimId);
        }

        function uploadDocs(claimId) {
            alert('Abriendo sistema de carga de documentos para: ' + claimId);
        }

        function contactAdjuster(name) {
            alert('Contactando a ' + name + '...');
        }

        function downloadApproval(claimId) {
            alert('Descargando carta de aprobación de: ' + claimId);
        }

        function downloadReceipt(claimId) {
            alert('Descargando comprobante de pago de: ' + claimId);
        }
    </script>
</body>
</html>