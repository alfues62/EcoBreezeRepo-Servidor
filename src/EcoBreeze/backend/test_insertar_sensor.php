<?php
session_start();

// Configura la zona horaria y el archivo de log
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';

// Variables para los mensajes de error
$error_message = '';

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php';

    // Obtiene los datos del formulario
    $usuario_id = $_POST['usuario_id'] ?? '';
    $mac = $_POST['mac'] ?? '';

    // Prepara los datos para la API
    $data = [
        'action' => 'insertar_sensor',
        'usuario_id' => $usuario_id,
        'mac' => $mac
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
            $success_message = htmlspecialchars($result['message']);
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
    <title>Insertar Sensor</title>
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
    <h1>Insertar Sensor</h1>

    <form action="" method="POST">
        <div class="form-group">
            <label for="usuario_id">ID de Usuario:</label>
            <input type="number" name="usuario_id" id="usuario_id" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="mac">MAC:</label>
            <input type="text" name="mac" id="mac" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Insertar Sensor</button>
    </form>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- Scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
