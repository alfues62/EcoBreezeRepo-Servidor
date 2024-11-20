<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_recuperar.css">
    <title>Verificación de Correo</title>
</head>
<body>
    <h1>Recuperar cuenta</h1>

    <!-- Si hay un mensaje de éxito, mostrarlo junto con el formulario -->
    <?php if (!$error && $message): ?>
        <div class="message success">
            <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <!-- Mostrar solo el formulario si el token es válido -->
        <form id="cambiarContrasenaRecuperar" method="post" action="/backend/restablecer_contrasena.php">
            <label for="nueva_contraseña">Nueva Contraseña:</label>
            <input type="password" id="nueva_contraseña" name="nueva_contraseña" required>

            <label for="confirmar_contraseña">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>

            <button type="submit">Restablecer Contraseña</button>
        </form>
    <?php elseif ($error): ?>
        <!-- Mostrar solo el mensaje de error si el token no es válido -->
        <div class="message error">
            <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
