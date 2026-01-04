<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Henriquez & Asociados</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: white;
            overflow: hidden;
        }

        /* ============================================
        OVERLAY DE TRANSICIÓN COMPLETA - PROFESIONAL
        ============================================ */
        
        .full-screen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #004B93;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            pointer-events: none;
        }

        .full-screen-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ============================================
        Panel deslizante - ANIMACIÓN PROFESIONAL
        ============================================ */

        .overlay-panel {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: #004B93;
            color: white;
            z-index: 100;
            transform: translateX(0);
            border-radius: 0 80px 80px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            /* Transición suave y profesional */
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        /* Estado final - Modo registro */
        .container.register-mode .overlay-panel {
            transform: translateX(100%);
            border-radius: 80px 0 0 80px;
        }

        /* ANIMACIÓN: De LOGIN a REGISTRO */
        @keyframes slideToRegister {
            0% {
                transform: translateX(0);
                width: 50%;
                border-radius: 0 80px 80px 0;
            }
            50% {
                transform: translateX(0);
                width: 100%;
                border-radius: 0;
            }
            100% {
                transform: translateX(100%);
                width: 50%;
                border-radius: 80px 0 0 80px;
            }
        }

        /* ANIMACIÓN: De REGISTRO a LOGIN */
        @keyframes slideToLogin {
            0% {
                transform: translateX(100%);
                width: 50%;
                border-radius: 80px 0 0 80px;
            }
            50% {
                transform: translateX(0);
                width: 100%;
                border-radius: 0;
            }
            100% {
                transform: translateX(0);
                width: 50%;
                border-radius: 0 80px 80px 0;
            }
        }

        /* Aplicar animaciones */
        .container.animating-to-register .overlay-panel {
            animation: slideToRegister 1s ease-in-out forwards;
        }

        .container.animating-to-login .overlay-panel {
            animation: slideToLogin 1s ease-in-out forwards;
        }

        /* Contenedor wrapper */
        .overlay-content {
            position: relative;
            width: 100%;
            max-width: 500px;
            min-height: 300px;
        }

        /* Ambos overlays superpuestos */
        .overlay-left,
        .overlay-right {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            text-align: center;
            transition: all 0.6s ease-in-out;
        }

        /* Estado inicial (LOGIN visible) */
        .overlay-left {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .overlay-right {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Estado REGISTRO */
        .container.register-mode .overlay-left {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .container.register-mode .overlay-right {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        /* Tipografía */
        .overlay-panel h2 {
            font-size: 42px;
            margin-bottom: 25px;
            font-weight: 600;
            line-height: 1.2;
        }

        .overlay-panel p {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.5;
        }

        /* Botón mejorado */
        .overlay-panel button {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 14px 50px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            outline: none;
            position: relative;
            overflow: hidden;
        }

        /* Efecto ripple en botón */
        .overlay-panel button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .overlay-panel button:hover::before {
            width: 300px;
            height: 300px;
        }

        .overlay-panel button:hover {
            background: white;
            color: #004B93;
            transform: scale(1.05);
        }

        .overlay-panel button:active {
            transform: scale(0.98);
        }

        /* ============================================
        CONTENEDORES DE FORMULARIOS - MEJORADOS
        ============================================ */

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 30px;
            overflow-y: auto;
        }

        .form-container.login-container {
            right: 0;
            z-index: 2;
            opacity: 1;
        }

        .form-container.register-container {
            left: 0;
            z-index: 1;
            opacity: 0;
            pointer-events: none;
            align-items: flex-start;
        }

        /* Durante transición - ocultar formularios */
        .container.animating-to-register .form-container,
        .container.animating-to-login .form-container {
            opacity: 0;
        }

        .container.register-mode .login-container {
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        .container.register-mode .register-container {
            opacity: 1;
            z-index: 2;
            pointer-events: all;
        }

        /* FORMULARIO */
        .form-box {
            width: 100%;
            max-width: 450px;
            padding: 20px 30px;
        }

        .form-box h3 {
            color: #333;
            font-size: 32px;
            margin-bottom: 30px;
            margin-top: 10px;
            text-align: center;
            font-weight: 700;
        }

        .input-group {
            position: relative;
            margin-bottom: 22px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 15px 18px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: #004B93;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 75, 147, 0.1);
        }

        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
            margin-top: 5px;
        }

        .forgot-password a {
            color: #004B93;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #003366;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: #004B93;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 75, 147, 0.4);
            background: #003366;
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .social-login {
            margin-top: 35px;
            text-align: center;
        }

        .social-login p {
            color: #999;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 45px;
            height: 45px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f7f7f7;
        }

        .social-icons a:hover {
            border-color: #004B93;
            background: #004B93;
            color: white;
            transform: translateY(-3px);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background-color: #fee;
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background-color: #d4edda;
            color: #28a745;
            border-left: 4px solid #28a745;
        }

        .campos-dinamicos {
            display: none;
        }

        .campos-dinamicos.active {
            display: block;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 13px;
            font-weight: 500;
        }

        .form-section {
            margin-bottom: 15px;
        }

        /* Estilos para validación en tiempo real */
        .input-group.valid input,
        .input-group.valid select {
            border-color: #28a745;
        }

        .input-group.invalid input,
        .input-group.invalid select {
            border-color: #dc3545;
        }

        .input-feedback {
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .input-feedback.show {
            display: block;
        }

        .input-feedback.error {
            color: #dc3545;
        }

        .input-feedback.success {
            color: #28a745;
        }

        /* Indicador de fortaleza de contraseña */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            background: #e8e8e8;
            overflow: hidden;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #28a745;
        }

        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            padding: 10px;
            background: #f7f7f7;
            border-radius: 5px;
            display: none;
        }

        .password-requirements.show {
            display: block;
        }

        .password-requirements ul {
            margin: 5px 0 0 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin: 3px 0;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.invalid {
            color: #dc3545;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .overlay-panel,
            .full-screen-overlay {
                display: none;
            }

            .form-container {
                width: 100%;
                position: relative;
            }

            .form-container.login-container,
            .form-container.register-container {
                opacity: 1;
                pointer-events: all;
            }

            .mobile-switch {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e8e8e8;
                display: block !important;
            }

            .mobile-switch p {
                color: #666;
                font-size: 14px;
                margin-bottom: 10px;
            }

            .mobile-switch a {
                color: #004B93;
                text-decoration: none;
                font-weight: 600;
                cursor: pointer;
            }

            .form-box {
                max-width: 420px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay de pantalla completa para transición -->
    <div class="full-screen-overlay" id="fullScreenOverlay"></div>

    <div class="container" id="container">
        
        <!-- FORMULARIO DE LOGIN -->
        <div class="form-container login-container">
            <div class="form-box">
                <h3>Iniciar Sesión</h3>
                
                <div class="alert alert-success" style="display: none;" id="successAlert">
                    Mensaje de éxito aquí
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="form_type" value="login">
                    
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Contraseña" required>
                    </div>
                    
                    <div class="forgot-password">
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="submit-btn">Ingresar</button>
                </form>

                <div class="social-login">
                    <p>or login with social platforms</p>
                    <div class="social-icons">
                        <a href="#" title="Google">G</a>
                        <a href="#" title="Facebook">f</a>
                        <a href="#" title="GitHub">⚡</a>
                        <a href="#" title="LinkedIn">in</a>
                    </div>
                </div>

                <div class="mobile-switch" style="display: none;">
                    <p>¿No tienes cuenta?</p>
                    <a onclick="showRegister()">Regístrate aquí</a>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE REGISTRO -->
        <div class="form-container register-container">
            <div class="form-box">
                <h3>Crear Cuenta</h3>
                
                <form method="POST" action="" id="registerForm">
                    <input type="hidden" name="form_type" value="register">
                    
                    <div class="form-section">
                        <div class="input-group">
                            <label>Tipo de Cliente *</label>
                            <select name="tipo_cliente" id="tipo_cliente" required onchange="toggleCampos()">
                                <option value="">Seleccione...</option>
                                <option value="Personal">Personal</option>
                                <option value="Empresa">Empresa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Campos para Personal -->
                    <div id="campos-personal" class="campos-dinamicos">
                        <div class="input-row">
                            <div class="input-group">
                                <input type="text" name="nombre" placeholder="Nombre" id="nombre">
                            </div>
                            <div class="input-group">
                                <input type="text" name="apellido" placeholder="Apellido" id="apellido">
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" name="cedula" placeholder="Cédula (X-XXX-XXXX)" id="cedula">
                        </div>
                    </div>

                    <!-- Campos para Empresa -->
                    <div id="campos-empresa" class="campos-dinamicos">
                        <div class="input-group">
                            <input type="text" name="razon_social" placeholder="Razón Social" id="razon_social">
                        </div>
                        <div class="input-group">
                            <input type="text" name="ruc" placeholder="RUC (Opcional)" id="ruc">
                        </div>
                    </div>

                    <!-- Campos comunes -->
                    <div class="form-section">
                        <div class="input-group">
                            <input type="email" name="reg_email" placeholder="Email *" required id="reg_email">
                            <div class="input-feedback" id="email-feedback"></div>
                        </div>
                        
                        <div class="input-row">
                            <div class="input-group">
                                <input type="tel" name="telefono" placeholder="Teléfono" id="telefono">
                            </div>
                            <div class="input-group">
                                <input type="tel" name="celular" placeholder="Celular *" required id="celular">
                                <div class="input-feedback" id="celular-feedback"></div>
                            </div>
                        </div>

                        <div class="input-group">
                            <select name="provincia">
                                <option value="">Provincia</option>
                                <option value="Panamá">Panamá</option>
                                <option value="Colón">Colón</option>
                                <option value="Chiriquí">Chiriquí</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <input type="text" name="direccion" placeholder="Dirección">
                        </div>

                        <div class="input-group">
                            <input type="password" name="reg_password" placeholder="Contraseña *" required minlength="8" id="reg_password">
                            <div class="password-strength" id="password-strength">
                                <div class="password-strength-bar" id="password-strength-bar"></div>
                            </div>
                            <div class="password-requirements" id="password-requirements">
                                <strong>La contraseña debe contener:</strong>
                                <ul>
                                    <li id="req-length" class="invalid">Mínimo 8 caracteres</li>
                                    <li id="req-uppercase" class="invalid">Una letra mayúscula</li>
                                    <li id="req-lowercase" class="invalid">Una letra minúscula</li>
                                    <li id="req-number" class="invalid">Un número</li>
                                    <li id="req-special" class="invalid">Un carácter especial (@#$%&*)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="input-group">
                            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña *" required minlength="8" id="confirm_password">
                            <div class="input-feedback" id="confirm-feedback"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Registrarme</button>
                </form>

                <div class="social-login">
                    <p>or register with social platforms</p>
                    <div class="social-icons">
                        <a href="#" title="Google">G</a>
                        <a href="#" title="Facebook">f</a>
                        <a href="#" title="GitHub">⚡</a>
                        <a href="#" title="LinkedIn">in</a>
                    </div>
                </div>

                <div class="mobile-switch" style="display: none;">
                    <p>¿Ya tienes cuenta?</p>
                    <a onclick="showLogin()">Inicia sesión aquí</a>
                </div>
            </div>
        </div>

        <!-- Panel deslizante -->
        <div class="overlay-panel">
            <div class="overlay-content">
                <!-- Overlay LEFT (Login - visible por defecto) -->
                <div class="overlay-left">
                    <h2>¡Hola, Bienvenido!</h2>
                    <p>¿No tienes una cuenta?</p>
                    <button type="button" onclick="showRegister()">Registrarse</button>
                </div>
                <!-- Overlay RIGHT (Registro - oculto por defecto) -->
                <div class="overlay-right">
                    <h2>¡Bienvenido de nuevo!</h2>
                    <p>¿Ya tienes una cuenta?</p>
                    <button type="button" onclick="showLogin()">Iniciar Sesión</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const fullScreenOverlay = document.getElementById('fullScreenOverlay');
        let isTransitioning = false;

        function showRegister() {
            if (isTransitioning) return;
            isTransitioning = true;
            
            console.log('🔵 Activando transición a REGISTRO');
            
            // Activar animación
            container.classList.add('animating-to-register');
            
            // Cambiar modo después de 500ms (mitad de la animación)
            setTimeout(() => {
                container.classList.add('register-mode');
            }, 500);
            
            // Limpiar después de completar
            setTimeout(() => {
                container.classList.remove('animating-to-register');
                isTransitioning = false;
                console.log('✅ Transición completada - Modo REGISTRO activo');
            }, 1000);
        }

        function showLogin() {
            if (isTransitioning) return;
            isTransitioning = true;
            
            console.log('🟢 Activando transición a LOGIN');
            
            // Activar animación
            container.classList.add('animating-to-login');
            
            // Cambiar modo después de 500ms (mitad de la animación)
            setTimeout(() => {
                container.classList.remove('register-mode');
            }, 500);
            
            // Limpiar después de completar
            setTimeout(() => {
                container.classList.remove('animating-to-login');
                isTransitioning = false;
                console.log('✅ Transición completada - Modo LOGIN activo');
            }, 1000);
        }

        function toggleCampos() {
            const tipoCliente = document.getElementById('tipo_cliente').value;
            const camposPersonal = document.getElementById('campos-personal');
            const camposEmpresa = document.getElementById('campos-empresa');
            
            camposPersonal.classList.remove('active');
            camposEmpresa.classList.remove('active');
            
            document.getElementById('nombre').required = false;
            document.getElementById('apellido').required = false;
            document.getElementById('cedula').required = false;
            document.getElementById('razon_social').required = false;
            
            if (tipoCliente === 'Personal') {
                camposPersonal.classList.add('active');
                document.getElementById('nombre').required = true;
                document.getElementById('apellido').required = true;
            } else if (tipoCliente === 'Empresa') {
                camposEmpresa.classList.add('active');
                document.getElementById('razon_social').required = true;
            }
        }

        // Log inicial
        console.log('🚀 Sistema de login profesional cargado');
        console.log('⚡ Transiciones mejoradas activadas');

        // ============================================
        // VALIDACIONES EN TIEMPO REAL
        // ============================================

        // Validación de fortaleza de contraseña
        const regPassword = document.getElementById('reg_password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordStrength = document.getElementById('password-strength');
        const passwordStrengthBar = document.getElementById('password-strength-bar');
        const passwordRequirements = document.getElementById('password-requirements');
        const confirmFeedback = document.getElementById('confirm-feedback');
        const registerForm = document.getElementById('registerForm');

        // Requisitos
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        // Mostrar requisitos al hacer focus
        regPassword.addEventListener('focus', function() {
            passwordRequirements.classList.add('show');
            passwordStrength.classList.add('show');
        });

        // Validar contraseña en tiempo real
        regPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Verificar cada requisito
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[@#$%&*!?]/.test(password);

            // Actualizar UI de requisitos
            reqLength.className = hasLength ? 'valid' : 'invalid';
            reqUppercase.className = hasUppercase ? 'valid' : 'invalid';
            reqLowercase.className = hasLowercase ? 'valid' : 'invalid';
            reqNumber.className = hasNumber ? 'valid' : 'invalid';
            reqSpecial.className = hasSpecial ? 'valid' : 'invalid';

            // Calcular fortaleza
            if (hasLength) strength++;
            if (hasUppercase) strength++;
            if (hasLowercase) strength++;
            if (hasNumber) strength++;
            if (hasSpecial) strength++;

            // Actualizar barra de fortaleza
            passwordStrengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                passwordStrengthBar.classList.add('weak');
            } else if (strength <= 4) {
                passwordStrengthBar.classList.add('medium');
            } else {
                passwordStrengthBar.classList.add('strong');
            }

            // Validar contraseña de confirmación si ya tiene contenido
            if (confirmPassword.value) {
                validateConfirmPassword();
            }
        });

        // Validar que las contraseñas coincidan
        function validateConfirmPassword() {
            const password = regPassword.value;
            const confirm = confirmPassword.value;
            const parent = confirmPassword.parentElement;

            if (confirm === '') {
                parent.classList.remove('valid', 'invalid');
                confirmFeedback.classList.remove('show');
                return;
            }

            if (password === confirm) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                confirmFeedback.className = 'input-feedback success show';
                confirmFeedback.textContent = '✓ Las contraseñas coinciden';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                confirmFeedback.className = 'input-feedback error show';
                confirmFeedback.textContent = '✗ Las contraseñas no coinciden';
            }
        }

        confirmPassword.addEventListener('input', validateConfirmPassword);

        // Validación de email
        const regEmail = document.getElementById('reg_email');
        const emailFeedback = document.getElementById('email-feedback');

        regEmail.addEventListener('blur', function() {
            const email = this.value;
            const parent = this.parentElement;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email) return;

            if (emailRegex.test(email)) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                emailFeedback.className = 'input-feedback success show';
                emailFeedback.textContent = '✓ Email válido';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                emailFeedback.className = 'input-feedback error show';
                emailFeedback.textContent = '✗ Email inválido';
            }
        });

        // Validación de celular panameño
        const celular = document.getElementById('celular');
        const celularFeedback = document.getElementById('celular-feedback');

        celular.addEventListener('input', function() {
            // Permitir solo números y guiones
            this.value = this.value.replace(/[^\d-]/g, '');
        });

        celular.addEventListener('blur', function() {
            const phone = this.value;
            const parent = this.parentElement;
            
            if (!phone) return;

            // Formato panameño: 8 dígitos (6XXX-XXXX o 2XX-XXXX, etc.)
            const phoneRegex = /^\d{4}-?\d{4}$/;

            if (phoneRegex.test(phone.replace(/-/g, ''))) {
                parent.classList.add('valid');
                parent.classList.remove('invalid');
                celularFeedback.className = 'input-feedback success show';
                celularFeedback.textContent = '✓ Número válido';
            } else {
                parent.classList.add('invalid');
                parent.classList.remove('valid');
                celularFeedback.className = 'input-feedback error show';
                celularFeedback.textContent = '✗ Formato: XXXX-XXXX';
            }
        });

        // Validación del formulario antes de enviar
        registerForm.addEventListener('submit', function(e) {
            const password = regPassword.value;
            const confirm = confirmPassword.value;

            // Verificar fortaleza de contraseña
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[@#$%&*!?]/.test(password);

            if (!hasLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecial) {
                e.preventDefault();
                alert('La contraseña no cumple con todos los requisitos de seguridad.');
                regPassword.focus();
                return false;
            }

            // Verificar que las contraseñas coincidan
            if (password !== confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                confirmPassword.focus();
                return false;
            }

            return true;
        });
    </script>
</body>
</html>