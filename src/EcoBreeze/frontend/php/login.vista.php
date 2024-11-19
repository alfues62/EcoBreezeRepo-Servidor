<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/login.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Iniciar Sesión</h1>

    <form action="main_login.php" method="POST">
    <label for="email">Correo electrónico:</label>
    <input type="email" name="email" id="email" class="form-control" required>
    
    <label for="contrasena">Contraseña:</label>
    <input type="password" name="contrasena" id="contrasena" class="form-control" required>

    <!-- Enlace para abrir el modal de recuperación de contraseña -->
    <div class="text-center mb-3">
        <a href="#" data-toggle="modal" data-target="#recoveryModal">¿Has olvidado tu contraseña?</a>
    </div>
    
    <!-- Campo hidden para la acción de login -->
    <input type="hidden" name="action" value="login">

    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    <button type="button" class="btn btn-secondary" onclick="window.location.href='/frontend/index.php'">Volver al Inicio</button>
    
    <!-- Campo hidden para el mensaje de error -->
    <input type="hidden" id="errorMessage" value="<?php echo htmlspecialchars($error_message ?? ''); ?>">
    </form>

    <!-- Modal para la recuperación de contraseña -->
    <div class="modal fade" id="recoveryModal" tabindex="-1" role="dialog" aria-labelledby="recoveryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recoveryModalLabel">Recuperación de Contraseña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="main_login.php" method="POST">
                        <label for="recoveryEmail">Ingresa tu correo electrónico:</label>
                        <input type="email" name="email" id="recuperar" class="form-control" required>
                        
                        <!-- Campo hidden para la acción de recuperación de contraseña -->
                        <input type="hidden" name="action" value="recuperar_contrasena">

                        <button type="submit" class="btn btn-primary mt-3">Recuperar Contraseña</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para mostrar errores -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
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

    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Incluyendo login.js después de jQuery y Bootstrap -->
    <script src="/frontend/js/login.js"></script>
</body>
</html>
