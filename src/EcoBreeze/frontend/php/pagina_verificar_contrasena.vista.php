<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3;url=/backend/login/main_login.php"> <!-- Redirección después de 3 segundos -->
    
    <title>Contraseña Restablecida</title>
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_recuperar.css">
</head>
<body>
    <h1>Contraseña Restablecida</h1>
        
    <!-- Mostrar el mensaje de éxito -->
    <div class="message success">
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <p>Serás redirigido automáticamente a la página de principal...</p>
</body>
</html>
