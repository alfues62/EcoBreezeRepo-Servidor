<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>

    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Core theme CSS -->
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/registro.css">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<body>


<!-- Navigation -->
<nav class="navbar fixed-top">
    <div class="navbar-brand">
        <a href="#seccionInicio">
            <img src="/frontend/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="/frontend/index.php" class="btn btn-volver">VOLVER</a></li>
        
    </ul>
</nav>

    <!-- Registro Section -->
    <section class="login-container">
        <h1>Bienvenido a EcoBreeze</h1>
        <h2>CREA TU CUENTA</h2>
        <form action="/backend/registrar/main_registro.php" method="POST" class="login-form">

            <div class="form-group">
            <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
            </div>

            <div class="form-group">
            <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos" required>
            </div>

            <div class="form-group">
            <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
            </div>
            
            <div class="form-group">
                <div class="password-wrapper">
                    <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" required/>
                    <div class="toggle-button">
                        <!-- Icono inicial (ojo abierto) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="eye-icon">
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                            <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="password-wrapper">
                    <input type="password" name="contrasena_confirmar" id="contrasena_confirmar" placeholder="Repite contraseña" required />
                    <div class="toggle-button">
                        <!-- Icono inicial (ojo abierto) -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="eye-icon">
                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                            <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Política de privacidad -->
            <div class="form-check mt-3">
                <input type="checkbox" id="politica" name="politica" class="form-check-input">
                <label for="politica" class="form-check-label">He leído y acepto la <a href="/backend/registrar/politica_privacidad.php" target="_blank">Política de Privacidad</a>.</label>
            </div>
            <button type="submit" class="btn btn-login">Registrar</button>
        </form>
        </section>

        <!-- Mensajes de éxito y error -->
        <div id="successMessage" style="display:none;">
            <?php echo isset($success_message) && $success_message != '' ? $success_message : ''; ?>
        </div>
        <div id="errorMessage" style="display:none;">
            <?php echo isset($error_message) && $error_message != '' ? $error_message : ''; ?>
        </div>

        <!-- Modal para registro exitoso -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Registro Exitoso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="successMessageContent"></p>
                    </div>
                    <div class="modal-footer">
                        <a href="/backend/login/main_login.php" class="btn btn-secondary">Ir a Login</a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para registro con error -->
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error en el Registro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="errorMessageContent"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/frontend/js/registro.js"></script>
</body>
</html>
