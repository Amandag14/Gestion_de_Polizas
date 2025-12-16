<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Henríquez y Asociados</title>
    <link rel="stylesheet" href="/web_polizas/public/css/auth/recuperar.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <!-- Vista de Formulario -->
            <div id="formView">
                <div class="icon-container">
                    <div class="icon">🔒</div>
                </div>

                <h2>Recuperar Contraseña</h2>
                <p class="auth-subtitle">
                    Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.
                </p>

                <!-- Alert Messages -->
                <div class="alert alert-error hidden" id="errorAlert">
                    ❌ <span id="errorMessage"></span>
                </div>

                <!-- Recovery Form -->
                <form id="recuperarForm">
                    <div class="form-group">
                        <label class="required">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="correo@ejemplo.com" 
                            required
                            autocomplete="email"
                        >
                        <span class="input-error" id="emailError">Por favor ingresa un correo válido</span>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn">
                        Enviar Instrucciones
                    </button>
                </form>

                <!-- Info Box -->
                <div class="info-box">
                    <h4>¿Qué sucederá después?</h4>
                    <ul>
                        <li>Recibirás un correo con un enlace de recuperación</li>
                        <li>El enlace será válido por 1 hora</li>
                        <li>Si no recibes el correo, revisa tu carpeta de spam</li>
                    </ul>
                </div>

                <!-- Footer Links -->
                <div class="auth-links">
                    <a href="login.php">Volver a Iniciar Sesión</a>
                    <a href="registro.php">¿No tienes cuenta? Regístrate</a>
                </div>
            </div>

            <!-- Vista de Éxito (oculta inicialmente) -->
            <div id="successView" class="success-view hidden">
                <div class="success-icon">✓</div>
                <h3 class="success-title">¡Correo Enviado!</h3>
                <p class="success-message">
                    Hemos enviado las instrucciones de recuperación a tu correo electrónico.
                    <br><br>
                    Por favor revisa tu bandeja de entrada y sigue los pasos indicados.
                    Si no recibes el correo en los próximos minutos, verifica tu carpeta de spam.
                </p>
                <a href="/login" class="btn-secondary">Volver al Login</a>
            </div>
        </div>
    </div>

    <script>
        const recuperarForm = document.getElementById('recuperarForm');
        const emailInput = document.getElementById('email');
        const formView = document.getElementById('formView');
        const successView = document.getElementById('successView');

        // Validación de email en tiempo real
        emailInput.addEventListener('blur', function() {
            validateEmail();
        });

        emailInput.addEventListener('input', function() {
            // Limpiar error mientras escribe
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                document.getElementById('emailError').classList.remove('show');
            }
        });

        function validateEmail() {
            const email = emailInput.value.trim();
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const errorSpan = document.getElementById('emailError');
            
            if (email && !pattern.test(email)) {
                emailInput.classList.add('error');
                errorSpan.classList.add('show');
                return false;
            } else {
                emailInput.classList.remove('error');
                errorSpan.classList.remove('show');
                return true;
            }
        }

        // Submit del formulario
        recuperarForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar email antes de enviar
            if (!validateEmail()) {
                mostrarError('Por favor ingresa un correo electrónico válido');
                return;
            }

            const email = emailInput.value.trim();
            
            if (!email) {
                mostrarError('Por favor ingresa tu correo electrónico');
                return;
            }
            
            // Deshabilitar botón
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            
            // Aquí iría la llamada al servidor
            // fetch('procesar-recuperar.php', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //     },
            //     body: JSON.stringify({ email: email })
            // })
            // .then(response => response.json())
            // .then(data => {
            //     if (data.success) {
            //         mostrarExito();
            //     } else {
            //         mostrarError(data.message);
            //         submitBtn.disabled = false;
            //         submitBtn.textContent = originalText;
            //     }
            // })
            // .catch(error => {
            //     mostrarError('Error al procesar la solicitud. Intenta nuevamente.');
            //     submitBtn.disabled = false;
            //     submitBtn.textContent = originalText;
            // });
            
            // Simulación del envío (eliminar en producción)
            setTimeout(() => {
                mostrarExito();
            }, 2000);
        });

        function mostrarError(mensaje) {
            const errorAlert = document.getElementById('errorAlert');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = mensaje;
            errorAlert.classList.remove('hidden');
            
            // Scroll al inicio para ver el error
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                errorAlert.classList.add('hidden');
            }, 5000);
        }

        function mostrarExito() {
            // Ocultar formulario y mostrar vista de éxito
            formView.classList.add('hidden');
            successView.classList.remove('hidden');
        }

        // Prevenir reenvío del formulario al recargar
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>