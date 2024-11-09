<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Usuario</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_usuario.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Menú de navegación con hamburguesa -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/frontend/index.php">Mi Aplicación</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#modificarPerfilModal">Modificar Perfil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/backend/logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </nav>

    <h1>Datos de Usuario</h1>

    <?php if ($usuario): ?>
        <h2 class="mt-5">Tus Datos</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['ID'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Nombre'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Apellidos'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($usuario['Email'] ?? 'N/A'); ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Modal para modificar el perfil -->
    <div class="modal fade" id="modificarPerfilModal" tabindex="-1" role="dialog" aria-labelledby="modificarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modificarPerfilModalLabel">Modificar Perfil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#cambiarContrasenaModal" data-dismiss="modal">Cambiar Contraseña</button>
                    <button type="button" class="btn btn-secondary mb-2" data-toggle="modal" data-target="#cambiarCorreoModal" data-dismiss="modal">Cambiar Correo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar la contraseña -->
    <div class="modal fade" id="cambiarContrasenaModal" tabindex="-1" role="dialog" aria-labelledby="cambiarContrasenaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarContrasenaModalLabel">Cambiar Contraseña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/pagina_usuario/main_usuario.php" method="POST">
                        <input type="hidden" name="action" value="cambiar_contrasena">
                        <div class="form-group">
                            <label for="contrasena_actual">Contraseña Actual:</label>
                            <input type="password" name="contrasena_actual" id="contrasena_actual" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="nueva_contrasena">Nueva Contraseña:</label>
                            <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmar_contrasena">Confirmar Nueva Contraseña:</label>
                            <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cambiar el correo electrónico -->
    <div class="modal fade" id="cambiarCorreoModal" tabindex="-1" role="dialog" aria-labelledby="cambiarCorreoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarCorreoModalLabel">Cambiar Correo Electrónico</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/pagina_usuario/main_usuario.php" method="POST">
                        <input type="hidden" name="action" value="cambiar_correo">
                        <div class="form-group">
                            <label for="email">Nuevo Correo Electrónico:</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($usuario['Email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contrasena_actual_correo">Contraseña Actual:</label>
                            <input type="password" name="contrasena_actual_correo" id="contrasena_actual_correo" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cambiar Correo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- Mensajes de éxito y error -->
        <div id="successMessage" style="display:none;">
            <?php echo isset($success_message) && $success_message != '' ? $success_message : ''; ?>
        </div>
        <div id="errorMessage" style="display:none;">
            <?php echo isset($error_message) && $error_message != '' ? $error_message : ''; ?>
        </div>

    <!-- Modal de Éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
                <div class="modal-footer">
                    <button onclick="redirectToSamePage()" type="button" class="btn btn-success" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

<!-- Scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Enlazar el archivo JavaScript personalizado -->
<script src="/frontend/js/pagina_usuario.js"></script>
</script>
</body>
</html>
