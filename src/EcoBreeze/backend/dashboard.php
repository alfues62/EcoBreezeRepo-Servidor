<?php
session_start();

// Configura la zona horaria y el archivo de log
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';

// Variables para los mensajes de error y éxito
$error_message = '';
$success_message = '';
$usuario = null;
$cambio_exitoso = false; // Variable para indicar si el cambio de contraseña fue exitoso

// Verifica si se ha enviado el formulario para cambiar la contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php';

    // Obtiene el ID del usuario de la sesión
    $id = $_SESSION['usuario_id'] ?? null; // ID del usuario de la sesión
    $contrasenaActual = $_POST['contrasena_actual'] ?? null; // Contraseña actual para el cambio
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? null; // Nueva contraseña para el cambio
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? null; // Confirmar nueva contraseña

    // Si hay nueva contraseña y confirmación, se prepara la solicitud para cambiarla
    if ($nuevaContrasena && $id && $nuevaContrasena === $confirmarContrasena) { // Asegúrate de que el ID y la confirmación estén presentes
        $data = [
            'action' => 'cambiar_contrasena',
            'id' => $id,
            'contrasena_actual' => $contrasenaActual,
            'nueva_contrasena' => $nuevaContrasena
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
                // Si el resultado es exitoso y es un cambio de contraseña
                $cambio_exitoso = true; // Se indica que el cambio fue exitoso
                $success_message = 'Contraseña cambiada con éxito.';
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
    } elseif ($nuevaContrasena && $id) {
        // Mensaje de error si las contraseñas no coinciden
        $error_message = 'Las nuevas contraseñas no coinciden.';
    }
}

// Obtiene los datos del usuario al cargar la página
if (isset($_SESSION['usuario_id'])) {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php';
    $id = $_SESSION['usuario_id']; // Asegúrate de que el ID del usuario se obtiene de la sesión

    // Preparar la solicitud para obtener los datos del usuario
    $data = [
        'action' => 'obtener_datos_usuario',
        'id' => $id
    ];

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
            $usuario = $result['usuario'];
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
} else {
    $error_message = 'No estás autenticado. Por favor, inicia sesión.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Usuario</title>
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
    <form action="index.php" method="GET" style="display: inline;">
        <button type="submit" class="btn btn-info">Ir al Inicio</button>
    </form>

    <!-- Botón para cerrar sesión -->
    <form action="logout.php" method="POST" style="display: inline;">
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
    </form>

    <!-- Formulario para cambiar la contraseña -->
    <h2 class="mt-5">Cambiar Contraseña</h2>
    <form action="" method="POST">
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
