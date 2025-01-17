<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/verificar_token.css">
    <title>Verificación de Correo</title>
</head>
<body>

    <h1>Verificación de Correo</h1>

    <div class="message <?php echo $error ? 'error' : 'success'; ?>">
        <?php if ($error): ?>
            <p><?php echo $error; ?></p>
        <?php else: ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

    <script src="/frontend/js/verificar_token.js"></script>
</body>
</html>
