<?php
session_start();

// Configura la zona horaria y el archivo de log
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';

// Redirige al usuario si no está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Variables para los mensajes de error
$error_message = '';

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_contrasena';

    // Sanitiza y prepara los datos de entrada
    $nueva_contrasena = trim($_POST['nueva_contrasena'] ?? '');
    $usuario_id = $_SESSION['usuario_id']; // Obtiene el ID del usuario de la sesión

    $data = [
        'usuario_id' => $usuario_id,
        'nueva_contrasena' => $nueva_contrasena
    ];

    // Intenta realizar la solicitud
    try {
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
            // Redirigir al usuario o mostrar un mensaje de éxito
            header('Location: dashboard.php');
            exit();
        } else {
            // Establece el mensaje de error según la respuesta de la API
            $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        }

    } catch (Exception $e) {
        // Registra el error en el archivo de log
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "{$timestamp} - Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);

        // Establece un mensaje de error genérico para el usuario
        $error_message = 'Ocurrió un error en el servidor. Inténtalo más tarde.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <h1>Cambiar Contraseña</h1>

    <form action="" method="POST">
        <label for="nueva_contrasena">Nueva Contraseña:</label>
        <input type="password" name="nueva_contrasena" id="nueva_contrasena" class="form-control" required>
        
        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
    </form>

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
                    <?php if ($error_message): ?>
                        <p><?php echo $error_message; ?></p>
                    <?php endif; ?>
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.
