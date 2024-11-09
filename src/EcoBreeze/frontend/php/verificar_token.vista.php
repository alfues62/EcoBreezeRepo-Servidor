<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/registro.css">
</head>
<body>

    <h1>Verificación de Correo</h1>

    <div class="message <?php echo $error ? 'error' : 'success'; ?>">
        <?php if ($error): ?>
            <p>Error: <?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>

    <div class="redirect-message">
        <?php if (!$error): ?>
            <p>Redirigiendo al login...</p>
        <?php else: ?>
            <p><a href="login.php">Volver al login</a></p>
        <?php endif; ?>
    </div>

    <script src="/frontend/js/verificar_token.js"></script>
</body>
</html>
