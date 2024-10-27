<?php
session_start();

// Configura la zona horaria y el archivo de log
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';

$error_message = '';
$success_message = '';

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php';

    // Sanitiza y prepara los datos de entrada
    $nombre = filter_var(trim($_POST['nombre'] ?? ''));
    $apellidos = filter_var(trim($_POST['apellidos'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena'] ?? '');
    $contrasena_confirmar = trim($_POST['contrasena_confirmar'] ?? '');

    // Validar que las contraseñas coincidan
    if ($contrasena !== $contrasena_confirmar) {
        $error_message = 'Las contraseñas no coinciden.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
        $error_message = 'El nombre solo puede contener letras y espacios.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $apellidos)) {
        $error_message = 'Los apellidos solo pueden contener letras y espacios.';
    } else {
        // Intenta realizar la solicitud
        try {
            $data = [
                'action' => 'registrar',
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'contrasena' => $contrasena,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            // Ejecuta la solicitud
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('CURL Error: ' . curl_error($ch));
            }
            curl_close($ch);

            // Decodificar la respuesta JSON
            $result = json_decode($response, true);

            // Verificar errores de decodificación JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            // Maneja la respuesta de la API
            if (isset($result['success']) && $result['success']) {
                $success_message = htmlspecialchars($result['message'] ?? 'Usuario registrado con éxito.');
            } else {
                $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            }

        } catch (Exception $e) {
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "{$timestamp} - Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $error_message = 'Ocurrió un error en el servidor. Inténtalo más tarde.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>

        <form action="" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="contrasena_confirmar">Confirmar Contraseña:</label>
                <input type="password" name="contrasena_confirmar" id="contrasena_confirmar" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Registrar</button>
        </form>

        <?php if ($success_message): ?>
            <div class="alert alert-success mt-3"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger mt-3"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
