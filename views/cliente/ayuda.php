<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Usuario';
$ultimo_ingreso = $_SESSION['ultimo_ingreso'] ?? null;

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

$primer_nombre = explode(' ', $user_name)[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Ayuda - Henríquez & Asociados</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
        }

        .page-header h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #1e3c72;
        }

        .page-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .search-box {
            max-width: 600px;
            margin: 1.5rem auto 0;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #004B93;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1.2rem;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .quick-link-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .quick-link-card:hover {
            border-color: #004B93;
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,75,147,0.15);
        }

        .quick-link-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .quick-link-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a2e57;
            margin-bottom: 0.5rem;
        }

        .quick-link-description {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
        }

        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .faq-header {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1a2e57;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .faq-category {
            margin-bottom: 2rem;
        }

        .category-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #004B93;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .faq-item {
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            padding: 1.25rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            transition: all 0.3s;
            user-select: none;
        }

        .faq-question:hover {
            background: #f0f7ff;
        }

        .question-text {
            font-weight: 600;
            color: #1a2e57;
            font-size: 0.95rem;
        }

        .faq-toggle {
            color: #004B93;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .faq-toggle.rotated {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            background: white;
        }

        .faq-answer.open {
            padding: 1.25rem;
            max-height: 500px;
        }

        .answer-text {
            color: #475569;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .contact-support {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
        }

        .contact-support h2 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .contact-support p {
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .contact-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .contact-btn {
            padding: 0.75rem 1.5rem;
            background: white;
            color: #004B93;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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

            .quick-links {
                grid-template-columns: 1fr;
            }

            .contact-buttons {
                flex-direction: column;
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
        <div class="page-header">
            <h1>Centro de Ayuda</h1>
            <p>Encuentra respuestas a tus preguntas más frecuentes</p>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="¿En qué podemos ayudarte?" id="searchInput">
            </div>
        </div>

        <div class="quick-links">
            <div class="quick-link-card" onclick="scrollToCategory('polizas')">
                <div class="quick-link-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="quick-link-title">Sobre Pólizas</div>
                <div class="quick-link-description">Información sobre tus pólizas, renovaciones y coberturas</div>
            </div>

            <div class="quick-link-card" onclick="scrollToCategory('pagos')">
                <div class="quick-link-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="quick-link-title">Pagos</div>
                <div class="quick-link-description">Métodos de pago, recibos y estados de cuenta</div>
            </div>

            <div class="quick-link-card" onclick="scrollToCategory('siniestros')">
                <div class="quick-link-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="quick-link-title">Siniestros</div>
                <div class="quick-link-description">Cómo reportar y dar seguimiento a tus reclamos</div>
            </div>

            <div class="quick-link-card" onclick="scrollToCategory('cuenta')">
                <div class="quick-link-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="quick-link-title">Mi Cuenta</div>
                <div class="quick-link-description">Gestión de perfil, contraseña y configuración</div>
            </div>
        </div>

        <div class="faq-section">
            <div class="faq-header">
                <i class="fas fa-question-circle"></i>
                Preguntas Frecuentes
            </div>

            <div class="faq-category" id="polizas">
                <div class="category-title">Sobre Pólizas</div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo puedo ver mis pólizas activas?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Puedes ver todas tus pólizas activas en el Dashboard principal. Desde ahí podrás consultar los detalles, fechas de vigencia, primas y descargar los documentos de cada póliza.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo renuevo mi póliza?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Te enviaremos notificaciones 30 días antes del vencimiento de tu póliza. Puedes renovarla directamente desde tu cuenta o contactar a tu corredor para asistencia personalizada.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Puedo modificar la cobertura de mi póliza?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Sí, puedes solicitar cambios en tu cobertura contactando a tu corredor. Algunos cambios pueden aplicarse de inmediato, mientras que otros requerirán esperar a la renovación de la póliza.</p>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="pagos">
                <div class="category-title">Sobre Pagos</div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Qué métodos de pago aceptan?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Aceptamos tarjetas de crédito/débito, transferencias bancarias y ACH. También puedes configurar pagos automáticos para mayor comodidad.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo descargo mis recibos de pago?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Los recibos están disponibles en la sección "Documentos" de tu cuenta. Puedes descargarlos en formato PDF para tus registros o para propósitos fiscales.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Qué pasa si no pago a tiempo?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Contamos con un período de gracia de 15 días. Después de este período, tu póliza podría ser suspendida. Te enviaremos recordatorios antes del vencimiento para evitar cualquier interrupción en tu cobertura.</p>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="siniestros">
                <div class="category-title">Siniestros y Reclamos</div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo reporto un siniestro?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Puedes reportar un siniestro desde la sección "Reclamos" en tu cuenta, llamando a la línea de emergencias 24/7 de la aseguradora, o contactando directamente a tu corredor. Es importante reportar el incidente lo antes posible.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cuánto tiempo tarda el proceso de reclamo?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">El tiempo varía según la complejidad del caso, pero generalmente los reclamos simples se resuelven en 15-30 días. Puedes dar seguimiento al estado de tu reclamo en tiempo real desde tu cuenta.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Qué documentos necesito para un reclamo?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Generalmente necesitarás: copia de tu póliza, fotos del daño, reporte policial (si aplica), facturas o estimados de reparación, y cualquier otro documento relacionado con el incidente.</p>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="cuenta">
                <div class="category-title">Mi Cuenta</div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo cambio mi contraseña?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Ve a la sección "Mi Perfil" y selecciona "Cambiar Contraseña". Necesitarás tu contraseña actual para confirmar el cambio. Te recomendamos usar una contraseña segura con al menos 8 caracteres.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Cómo actualizo mis datos de contacto?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Puedes actualizar tu teléfono, correo electrónico y dirección desde la sección "Mi Perfil". Es importante mantener tus datos actualizados para recibir notificaciones importantes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <span class="question-text">¿Es segura mi información?</span>
                        <i class="fas fa-chevron-down faq-toggle"></i>
                    </div>
                    <div class="faq-answer">
                        <p class="answer-text">Absolutamente. Utilizamos encriptación de nivel bancario y cumplimos con todas las regulaciones de protección de datos. Tu información personal y financiera está completamente segura.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-support">
            <h2>¿No encontraste lo que buscabas?</h2>
            <p>Nuestro equipo está listo para ayudarte</p>
            <div class="contact-buttons">
                <a href="contacto.php" class="contact-btn">
                    <i class="fas fa-user-tie"></i>
                    Contactar a mi Corredor
                </a>
                <a href="tel:+5076123-4567" class="contact-btn">
                    <i class="fas fa-phone"></i>
                    Llamar Ahora
                </a>
                <a href="https://wa.me/50761234567" target="_blank" class="contact-btn">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
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
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const toggle = element.querySelector('.faq-toggle');
            
            const allAnswers = document.querySelectorAll('.faq-answer');
            const allToggles = document.querySelectorAll('.faq-toggle');
            
            allAnswers.forEach(a => {
                if (a !== answer) {
                    a.classList.remove('open');
                }
            });
            
            allToggles.forEach(t => {
                if (t !== toggle) {
                    t.classList.remove('rotated');
                }
            });
            
            answer.classList.toggle('open');
            toggle.classList.toggle('rotated');
        }

        function scrollToCategory(categoryId) {
            const element = document.getElementById(categoryId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Búsqueda simple en FAQs
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>