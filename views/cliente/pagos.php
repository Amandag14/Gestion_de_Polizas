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

$primer_nombre = explode(' ', $user_name)[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar un Pago - Henríquez & Asociados</title>
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

        .payment-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a2e57;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .policy-select-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .policy-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .policy-card:hover {
            border-color: #004B93;
            box-shadow: 0 4px 12px rgba(0,75,147,0.1);
        }

        .policy-card.selected {
            border-color: #004B93;
            background: #f0f7ff;
        }

        .policy-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .policy-type {
            font-weight: 600;
            color: #1a2e57;
            font-size: 0.95rem;
        }

        .policy-number {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .policy-radio {
            width: 20px;
            height: 20px;
            accent-color: #004B93;
        }

        .policy-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .policy-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .detail-label {
            color: #64748b;
        }

        .detail-value {
            font-weight: 600;
            color: #1a2e57;
        }

        .amount-highlight {
            color: #10b981;
            font-size: 1.1rem;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .method-card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .method-card:hover {
            border-color: #004B93;
            box-shadow: 0 4px 12px rgba(0,75,147,0.1);
        }

        .method-card.selected {
            border-color: #004B93;
            background: #f0f7ff;
        }

        .method-icon {
            font-size: 2.5rem;
            color: #004B93;
            margin-bottom: 0.75rem;
        }

        .method-name {
            font-weight: 600;
            color: #1a2e57;
            margin-bottom: 0.25rem;
        }

        .method-description {
            font-size: 0.8rem;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1a2e57;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #004B93;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 1rem;
        }

        .payment-summary {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1a2e57;
            padding-top: 1rem;
        }

        .summary-label {
            color: #64748b;
        }

        .summary-value {
            font-weight: 600;
            color: #1a2e57;
        }

        .btn-primary {
            background: linear-gradient(135deg, #004B93 0%, #0066B3 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,75,147,0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .security-notice {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #f0f7ff;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid #004B93;
        }

        .security-notice i {
            color: #004B93;
            font-size: 1.25rem;
        }

        .security-notice-text {
            font-size: 0.85rem;
            color: #1a2e57;
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .payment-methods {
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
            <h1>Realizar un Pago</h1>
            <p>Selecciona la póliza que deseas pagar y completa el proceso de forma segura</p>
        </div>

        <div class="payment-section">
            <div class="section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Selecciona la Póliza
            </div>

            <div class="policy-select-grid">
                <div class="policy-card selected" onclick="selectPolicy(this, 1)">
                    <div class="policy-card-header">
                        <div>
                            <div class="policy-type">SEGURO DE VIDA INDIVIDUAL</div>
                            <div class="policy-number">VID-2024-001234</div>
                        </div>
                        <input type="radio" name="policy" class="policy-radio" checked>
                    </div>
                    <div class="policy-details">
                        <div class="policy-detail-row">
                            <span class="detail-label">Próximo pago:</span>
                            <span class="detail-value">10/01/2025</span>
                        </div>
                        <div class="policy-detail-row">
                            <span class="detail-label">Monto:</span>
                            <span class="detail-value amount-highlight">$1,200.00</span>
                        </div>
                    </div>
                </div>

                <div class="policy-card" onclick="selectPolicy(this, 2)">
                    <div class="policy-card-header">
                        <div>
                            <div class="policy-type">SEGURO DE AUTO</div>
                            <div class="policy-number">AUT-2024-005678</div>
                        </div>
                        <input type="radio" name="policy" class="policy-radio">
                    </div>
                    <div class="policy-details">
                        <div class="policy-detail-row">
                            <span class="detail-label">Próximo pago:</span>
                            <span class="detail-value">15/02/2025</span>
                        </div>
                        <div class="policy-detail-row">
                            <span class="detail-label">Monto:</span>
                            <span class="detail-value amount-highlight">$850.00</span>
                        </div>
                    </div>
                </div>

                <div class="policy-card" onclick="selectPolicy(this, 3)">
                    <div class="policy-card-header">
                        <div>
                            <div class="policy-type">SEGURO DE HOGAR</div>
                            <div class="policy-number">HOG-2024-009012</div>
                        </div>
                        <input type="radio" name="policy" class="policy-radio">
                    </div>
                    <div class="policy-details">
                        <div class="policy-detail-row">
                            <span class="detail-label">Próximo pago:</span>
                            <span class="detail-value">20/03/2025</span>
                        </div>
                        <div class="policy-detail-row">
                            <span class="detail-label">Monto:</span>
                            <span class="detail-value amount-highlight">$650.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="payment-section">
            <div class="section-title">
                <i class="fas fa-credit-card"></i>
                Método de Pago
            </div>

            <div class="payment-methods">
                <div class="method-card selected" onclick="selectMethod(this, 'card')">
                    <div class="method-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="method-name">Tarjeta de Crédito/Débito</div>
                    <div class="method-description">Pago inmediato</div>
                </div>

                <div class="method-card" onclick="selectMethod(this, 'transfer')">
                    <div class="method-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="method-name">Transferencia Bancaria</div>
                    <div class="method-description">1-2 días hábiles</div>
                </div>

                <div class="method-card" onclick="selectMethod(this, 'ach')">
                    <div class="method-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="method-name">ACH</div>
                    <div class="method-description">Débito automático</div>
                </div>
            </div>

            <div id="cardForm" style="display: block;">
                <div class="form-group">
                    <label class="form-label">Número de Tarjeta</label>
                    <input type="text" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19">
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre en la Tarjeta</label>
                    <input type="text" class="form-input" placeholder="Juan Pérez">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Fecha de Expiración</label>
                        <input type="text" class="form-input" placeholder="MM/AA" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-input" placeholder="123" maxlength="4">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Código Postal</label>
                        <input type="text" class="form-input" placeholder="12345">
                    </div>
                </div>
            </div>

            <div id="transferForm" style="display: none;">
                <p style="color: #64748b; margin-bottom: 1rem;">
                    Por favor realiza la transferencia a la siguiente cuenta y envía el comprobante:
                </p>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">
                    <div style="margin-bottom: 0.75rem;">
                        <strong>Banco:</strong> Banco General
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <strong>Cuenta:</strong> 04-12-34-567890
                    </div>
                    <div>
                        <strong>Beneficiario:</strong> Henríquez & Asociados S.A.
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Adjuntar Comprobante</label>
                    <input type="file" class="form-input" accept=".pdf,.jpg,.png">
                </div>
            </div>

            <div id="achForm" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Banco</label>
                    <select class="form-input">
                        <option>Selecciona tu banco</option>
                        <option>Banco General</option>
                        <option>Banco Nacional</option>
                        <option>BAC</option>
                        <option>Banistmo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Número de Cuenta</label>
                    <input type="text" class="form-input" placeholder="Ingresa tu número de cuenta">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Cuenta</label>
                    <select class="form-input">
                        <option>Selecciona el tipo</option>
                        <option>Corriente</option>
                        <option>Ahorros</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="payment-section">
            <div class="section-title">
                <i class="fas fa-receipt"></i>
                Resumen del Pago
            </div>

            <div class="payment-summary">
                <div class="summary-row">
                    <span class="summary-label">Póliza:</span>
                    <span class="summary-value">SEGURO DE VIDA INDIVIDUAL</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Número de póliza:</span>
                    <span class="summary-value">VID-2024-001234</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Prima:</span>
                    <span class="summary-value">$1,200.00</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Método de pago:</span>
                    <span class="summary-value">Tarjeta de Crédito</span>
                </div>
                <div class="summary-row">
                    <span>Total a Pagar:</span>
                    <span style="color: #10b981;">$1,200.00</span>
                </div>
            </div>

            <button class="btn-primary" onclick="processPayment()">
                <i class="fas fa-lock"></i> Procesar Pago Seguro
            </button>

            <div class="security-notice">
                <i class="fas fa-shield-alt"></i>
                <div class="security-notice-text">
                    <strong>Pago 100% seguro.</strong> Todos los datos son encriptados y protegidos según los estándares PCI DSS.
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
        function selectPolicy(card, id) {
            document.querySelectorAll('.policy-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type="radio"]').checked = true;
        }

        function selectMethod(card, method) {
            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            
            document.getElementById('cardForm').style.display = 'none';
            document.getElementById('transferForm').style.display = 'none';
            document.getElementById('achForm').style.display = 'none';
            
            if (method === 'card') {
                document.getElementById('cardForm').style.display = 'block';
            } else if (method === 'transfer') {
                document.getElementById('transferForm').style.display = 'block';
            } else if (method === 'ach') {
                document.getElementById('achForm').style.display = 'block';
            }
        }

        function processPayment() {
            if (confirm('¿Estás seguro de procesar este pago?')) {
                alert('Procesando pago... Esta funcionalidad será implementada próximamente.');
                // Aquí iría la lógica real de procesamiento de pago
            }
        }
    </script>
</body>
</html>