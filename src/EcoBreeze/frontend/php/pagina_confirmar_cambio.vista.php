<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBreeze</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .message {
            margin-top: 20px;
            font-size: 1.2em;
        }
    </style>
    <script>
        // Redirigir a la página de cierre de sesión después de 3 segundos
        setTimeout(function() {
            window.location.href = '/backend/logout.php';
        }, 3000);
    </script>
</head>
<body>
    <h1>EcoBreeze</h1>
    <div class="message">
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php elseif (!empty($message)): ?>
            <p style="color: green;"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>