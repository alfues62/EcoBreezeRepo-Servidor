<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Usuario</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/pagina_usuario.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pagina_usuario.css"> <!-- Enlazamos con el archivo CSS -->
</head>
<body>
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

    <!-- Botón para ir al index sin cerrar sesión -->
    <form action="/frontend/index.php" method="GET" style="display: inline;">
        <button type="submit" class="btn btn-info">Ir al Inicio</button>
    </form>

    <!-- Botón para cerrar sesión -->
    <form action="logout.php" method="POST" style="display: inline;">
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
    </form>

    <!-- Formulario para cambiar la contraseña -->
    <h2 class="mt-5">Cambiar Contraseña</h2>
    <form action="/backend/cambiar_contrasena.php" method="POST">
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

    <?php if ($error_message): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
</body>
</html>
