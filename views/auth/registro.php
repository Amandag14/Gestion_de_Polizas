<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Henríquez y Asociados</title>
    <link rel="stylesheet" href="/Gestion_de_Polizas/public/css/auth/registro.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Crear Cuenta</h2>
            <p class="auth-subtitle">Registra tus datos para acceder al portal</p>

            <div class="alert alert-error hidden" id="errorAlert">
                ❌ <span id="errorMessage"></span>
            </div>

            <div class="alert alert-success hidden" id="successAlert">
                ✓ Registro exitoso. Espera la aprobación del administrador.
            </div>

            <!-- Registration Form -->
            <form id="registroForm">
                <!-- Tipo de Cliente -->
                <div class="form-group">
                    <label class="required">Tipo de Cliente</label>
                    <select id="tipoCliente" name="tipo_cliente" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Personal">Personal</option>
                        <option value="Empresa">Empresa</option>
                    </select>
                </div>

                <!-- Nombre y Apellido (Personal) -->
                <div id="personalFields">
                    <div class="form-group half">
                        <label class="required">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Juan Carlos">
                    </div>
                    <div class="form-group half">
                        <label class="required">Apellido</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Delgado">
                    </div>
                </div>

                <!-- Razón Social (Empresa) -->
                <div class="form-group hidden" id="empresaFields">
                    <label class="required">Razón Social</label>
                    <input type="text" id="razonSocial" name="razon_social" placeholder="Empresa S.A.">
                </div>

                <!-- Identificación -->
                <div class="form-group" id="cedulaField">
                    <label class="required">Cédula</label>
                    <input type="text" id="cedula" name="cedula" placeholder="8-123-4567" pattern="[0-9]{1,2}-[0-9]{3,4}-[0-9]{4}">
                    <span class="input-help">Formato: X-XXX-XXXX</span>
                    <span class="input-error" id="cedulaError">Formato de cédula inválido</span>
                </div>

                <div class="form-group hidden" id="rucField">
                    <label>RUC</label>
                    <input type="text" id="ruc" name="ruc" placeholder="12345-67-890123">
                    <span class="input-help">Opcional para empresas</span>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="required">Correo Electrónico</label>
                    <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
                    <span class="input-error" id="emailError">Correo electrónico inválido</span>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label class="required">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Ingresa una contraseña</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="required">Confirmar Contraseña</label>
                    <input type="password" id="confirmPassword" name="confirm_password" placeholder="Repite tu contraseña" required>
                    <span class="input-error" id="passwordError">Las contraseñas no coinciden</span>
                </div>

                <!-- Teléfonos -->
                <div class="form-group half">
                    <label>Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="+507 XXXX-XXXX">
                </div>

                <div class="form-group half">
                    <label class="required">Celular</label>
                    <input type="tel" id="celular" name="celular" placeholder="+507 6XXX-XXXX" required>
                </div>

                <!-- Dirección -->
                <div class="form-group">
                    <label>Provincia</label>
                    <select id="provincia" name="provincia">
                        <option value="">Selecciona una provincia</option>
                        <option value="Panama">Panamá</option>
                        <option value="Colon">Colón</option>
                        <option value="Chiriqui">Chiriquí</option>
                        <option value="Bocas del Toro">Bocas del Toro</option>
                        <option value="Veraguas">Veraguas</option>
                        <option value="Cocle">Coclé</option>
                        <option value="Herrera">Herrera</option>
                        <option value="Los Santos">Los Santos</option>
                        <option value="Darien">Darién</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <textarea id="direccion" name="direccion" placeholder="Calle, edificio, número de casa..."></textarea>
                </div>

                <!-- Términos y Condiciones -->
                <div class="checkbox-group">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">
                        Acepto los <a href="/terminos" target="_blank">Términos y Condiciones</a> 
                        y la <a href="/privacidad" target="_blank">Política de Privacidad</a>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary" id="submitBtn">
                    Crear Cuenta
                </button>
            </form>

            <!-- Footer -->
            <div class="auth-links">
                <a href="web_polizas/views/auth/login.php">¿Ya tienes cuenta? Inicia Sesión</a>
                <a href="web_polizas/views/auth/recuperar.php">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>

    <script>
        const registroForm = document.getElementById('registroForm');
        const tipoCliente = document.getElementById('tipoCliente');
        const personalFields = document.getElementById('personalFields');
        const empresaFields = document.getElementById('empresaFields');
        const cedulaField = document.getElementById('cedulaField');
        const rucField = document.getElementById('rucField');

        // Cambiar campos según tipo de cliente
        tipoCliente.addEventListener('change', function() {
            if (this.value === 'Empresa') {
                personalFields.classList.add('hidden');
                empresaFields.classList.remove('hidden');
                cedulaField.classList.add('hidden');
                rucField.classList.remove('hidden');
                
                document.getElementById('nombre').required = false;
                document.getElementById('apellido').required = false;
                document.getElementById('razonSocial').required = true;
            } else if (this.value === 'Personal') {
                personalFields.classList.remove('hidden');
                empresaFields.classList.add('hidden');
                cedulaField.classList.remove('hidden');
                rucField.classList.add('hidden');
                
                document.getElementById('nombre').required = true;
                document.getElementById('apellido').required = true;
                document.getElementById('razonSocial').required = false;
            }
        });

        // Validación de cédula
        document.getElementById('cedula').addEventListener('blur', function() {
            const cedula = this.value;
            const pattern = /^[0-9]{1,2}-[0-9]{3,4}-[0-9]{4}$/;
            const errorSpan = document.getElementById('cedulaError');
            
            if (cedula && !pattern.test(cedula)) {
                this.classList.add('error');
                errorSpan.classList.add('show');
            } else {
                this.classList.remove('error');
                errorSpan.classList.remove('show');
            }
        });

        // Validación de email
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const errorSpan = document.getElementById('emailError');
            
            if (email && !pattern.test(email)) {
                this.classList.add('error');
                errorSpan.classList.add('show');
            } else {
                this.classList.remove('error');
                errorSpan.classList.remove('show');
            }
        });

        // Medidor de fortaleza de contraseña
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthFill.className = 'strength-fill';
            
            if (strength === 0) {
                strengthText.textContent = 'Muy débil';
            } else if (strength <= 2) {
                strengthFill.classList.add('weak');
                strengthText.textContent = 'Débil - Agrega mayúsculas, números o símbolos';
            } else if (strength === 3) {
                strengthFill.classList.add('medium');
                strengthText.textContent = 'Media - Considera agregar caracteres especiales';
            } else {
                strengthFill.classList.add('strong');
                strengthText.textContent = '¡Excelente! Contraseña fuerte';
            }
        });

        // Validar que las contraseñas coincidan
        document.getElementById('confirmPassword').addEventListener('blur', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorSpan = document.getElementById('passwordError');
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('error');
                errorSpan.classList.add('show');
            } else {
                this.classList.remove('error');
                errorSpan.classList.remove('show');
            }
        });

        // Submit del formulario
        registroForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                mostrarError('Las contraseñas no coinciden');
                return;
            }
            
            if (!document.getElementById('terminos').checked) {
                mostrarError('Debes aceptar los términos y condiciones');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registrando...';
            
            // Simulación de registro
            setTimeout(() => {
                document.getElementById('infoAlert').classList.add('hidden');
                document.getElementById('errorAlert').classList.add('hidden');
                document.getElementById('successAlert').classList.remove('hidden');
                registroForm.reset();
                submitBtn.disabled = false;
                submitBtn.textContent = 'Crear Cuenta';
                
                setTimeout(() => {
                    window.location.href = '/login';
                }, 3000);
            }, 2000);
        });

        function mostrarError(mensaje) {
            const errorAlert = document.getElementById('errorAlert');
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = mensaje;
            errorAlert.classList.remove('hidden');
            
            setTimeout(() => {
                errorAlert.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>