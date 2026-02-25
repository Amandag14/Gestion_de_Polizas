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

// Calcular tiempo desde el login usando $_SESSION['login_time']
$ultimo_ingreso_texto = 'Primer ingreso';
if (isset($_SESSION['login_time'])) {
    $segundos = time() - $_SESSION['login_time'];
    $minutos  = (int)($segundos / 60);
    $horas    = (int)($segundos / 3600);
    $dias     = (int)($segundos / 86400);

    if ($segundos < 60) {
        $ultimo_ingreso_texto = "Hace unos segundos";
    } elseif ($minutos < 60) {
        $ultimo_ingreso_texto = "Hace $minutos minuto" . ($minutos > 1 ? 's' : '');
    } elseif ($horas < 24) {
        $ultimo_ingreso_texto = "Hace $horas hora" . ($horas > 1 ? 's' : '');
    } elseif ($dias < 30) {
        $ultimo_ingreso_texto = "Hace $dias día" . ($dias > 1 ? 's' : '');
    } else {
        $ultimo_ingreso_texto = date('d/m/Y H:i', $_SESSION['login_time']);
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
    <title>Mis Documentos - Henríquez & Asociados</title>
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

        .page-title {
            font-size: 32px;
            color: #0d5ba8;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .filters {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #1976d2;
        }

        .btn-filter {
            background: #1976d2;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-filter:hover {
            background: #0d5ba8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25,118,210,0.3);
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .document-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #1976d2;
            transition: all 0.3s ease;
            position: relative;
        }

        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .document-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .document-type {
            font-size: 12px;
            color: #1976d2;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .document-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .document-date {
            font-size: 13px;
            color: #999;
            margin-bottom: 20px;
        }

        .document-actions {
            display: flex;
            gap: 10px;
        }

        .btn-download,
        .btn-view {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-download {
            background: #1976d2;
            color: white;
        }

        .btn-download:hover {
            background: #0d5ba8;
        }

        .btn-view {
            background: #f5f7fa;
            color: #1976d2;
            border: 1px solid #1976d2;
        }

        .btn-view:hover {
            background: #1976d2;
            color: white;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .btn-download-all {
            background: white;
            color: #1976d2;
            border: 2px solid #1976d2;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-download-all:hover {
            background: #1976d2;
            color: white;
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

            .filters {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .documents-grid {
                grid-template-columns: 1fr;
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
                <a href="documentos.php" class="nav-item active">Documentos</a>
                <a href="reclamos.php" class="nav-item">Reclamos</a>
                <a href="perfil.php" class="nav-item">Mi Perfil</a>
            </div>
            <div class="user-info">
                <div class="user-name">Hola, <?php echo htmlspecialchars(strtoupper($user_name)); ?></div>
                <div class="user-time"><?php echo htmlspecialchars($ultimo_ingreso_texto); ?></div>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Mis Documentos</h1>
        <p class="page-subtitle">Accede y descarga todos tus documentos de forma rápida y segura</p>

        <div class="filters">
            <div class="filter-group">
                <label>Tipo de Documento</label>
                <select id="filterType">
                    <option value="">Todos los tipos</option>
                    <option value="certificado">Certificado de Póliza</option>
                    <option value="recibo">Recibo de Pago</option>
                    <option value="constancia">Constancia de Cobertura</option>
                    <option value="fiscal">Documento Fiscal</option>
                    <option value="condiciones">Condiciones Generales</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Póliza</label>
                <select id="filterPoliza">
                    <option value="">Todas las pólizas</option>
                    <option value="vida">Seguro de Vida Individual</option>
                    <option value="auto">Seguro de Auto</option>
                    <option value="hogar">Seguro de Hogar</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Fecha</label>
                <input type="month" id="filterDate">
            </div>

            <button class="btn-filter" onclick="filterDocuments()">Buscar</button>
        </div>

        <div class="section-header">
            <div class="section-title">Documentos Recientes</div>
            <button class="btn-download-all">
                <i class="fas fa-download"></i>
                Descargar Todo
            </button>
        </div>

        <div class="documents-grid" id="documentsGrid">
            <!-- Certificado de Póliza -->
            <div class="document-card" data-type="certificado" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="document-type">Certificado de Póliza</div>
                <div class="document-title">Seguro de Vida Individual</div>
                <div class="document-meta">VID-2024-001234</div>
                <div class="document-date"><i class="far fa-calendar"></i> Generado: 05 de Enero, 2025</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('certificado-vida')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('certificado-vida')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>

            <!-- Recibo de Pago -->
            <div class="document-card" data-type="recibo" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="document-type">Recibo de Pago</div>
                <div class="document-title">Pago Prima Anual</div>
                <div class="document-meta">Recibo #000123 - $1,200.00</div>
                <div class="document-date"><i class="far fa-calendar"></i> Fecha: 10 de Diciembre, 2024</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('recibo-001')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('recibo-001')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>

            <!-- Constancia de Cobertura -->
            <div class="document-card" data-type="constancia" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="document-type">Constancia de Cobertura</div>
                <div class="document-title">Certificación de Seguro Vigente</div>
                <div class="document-meta">VID-2024-001234</div>
                <div class="document-date"><i class="far fa-calendar"></i> Vigencia: Hasta 10/01/2025</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('constancia-001')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('constancia-001')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>

            <!-- Condiciones Generales -->
            <div class="document-card" data-type="condiciones" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="document-type">Condiciones Generales</div>
                <div class="document-title">Términos y Condiciones de Póliza</div>
                <div class="document-meta">Seguro de Vida Individual</div>
                <div class="document-date"><i class="far fa-calendar"></i> Versión: Enero 2024</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('condiciones-vida')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('condiciones-vida')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>

            <!-- Documento Fiscal -->
            <div class="document-card" data-type="fiscal" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="document-type">Documento Fiscal</div>
                <div class="document-title">Comprobante para Declaración</div>
                <div class="document-meta">Año Fiscal 2024</div>
                <div class="document-date"><i class="far fa-calendar"></i> Período: Enero - Diciembre 2024</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('fiscal-2024')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('fiscal-2024')">
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>

            <!-- Carta de No Siniestralidad -->
            <div class="document-card" data-type="constancia" data-poliza="vida">
                <div class="document-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="document-type">Constancia</div>
                <div class="document-title">Carta de No Siniestralidad</div>
                <div class="document-meta">VID-2024-001234</div>
                <div class="document-date"><i class="far fa-calendar"></i> Generado: 28 de Diciembre, 2024</div>
                <div class="document-actions">
                    <button class="btn-view" onclick="viewDocument('no-siniestro')">
                        <i class="far fa-eye"></i> Ver
                    </button>
                    <button class="btn-download" onclick="downloadDocument('no-siniestro')">
                        <i class="fas fa-download"></i> Descargar
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
        function filterDocuments() {
            const type = document.getElementById('filterType').value;
            const poliza = document.getElementById('filterPoliza').value;
            const cards = document.querySelectorAll('.document-card');
            
            cards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                const cardPoliza = card.getAttribute('data-poliza');
                
                let showCard = true;
                
                if (type && cardType !== type) {
                    showCard = false;
                }
                
                if (poliza && cardPoliza !== poliza) {
                    showCard = false;
                }
                
                card.style.display = showCard ? 'block' : 'none';
            });
        }

        function viewDocument(docId) {
            alert('Abriendo vista previa del documento: ' + docId);
            // Aquí iría la lógica para abrir el documento en un modal o nueva pestaña
        }

        function downloadDocument(docId) {
            alert('Descargando documento: ' + docId);
            // Aquí iría la lógica para descargar el archivo
        }
    </script>
</body>
</html>