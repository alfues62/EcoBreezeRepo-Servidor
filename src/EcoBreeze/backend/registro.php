<?php
// Inicializa la variable de mensaje
$message = '';
$show_modal = false; // Variable para controlar la ventana emergente

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Ruta del archivo de log
    $logFile = '/var/www/html/logs/app.log';

    // Validar que las contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $message = "Las contraseñas no coinciden.";
        error_log("[" . date('Y-m-d H:i:s') . "] Error: Las contraseñas no coinciden.\n", 3, $logFile);
        $show_modal = true; // Activar el modal
    } else {
        // Hash de la contraseña
        $contrasenaHash = password_hash($contrasena, PASSWORD_BCRYPT);

        // Preparar los datos para enviar a la API
        $data = [
            'Nombre' => $nombre,
            'Apellidos' => $apellidos,
            'Email' => $email,
            'ContrasenaHash' => $contrasenaHash,
            'ROL_RolID' => 2
        ];

        // Iniciar una solicitud cURL para enviar los datos a la API
        $ch = curl_init('http://host.docker.internal:8080/api/api_usuario.php');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Obtener la respuesta de la API
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Decodificar la respuesta
        $resultado = json_decode($response, true);

        // Registro en el log de errores o respuestas inesperadas
        if ($httpcode !== 200 || isset($curl_error) && $curl_error) {
            error_log("[" . date('Y-m-d H:i:s') . "] Error en la solicitud cURL. Código HTTP: $httpcode. Error cURL: $curl_error\n", 3, $logFile);
        }

        if ($httpcode === 200 && isset($resultado['success'])) {
            $message = "Usuario registrado exitosamente.";
        } else {
            $message = "Error al registrar usuario.";
            error_log("[" . date('Y-m-d H:i:s') . "] Error en la respuesta de la API: " . ($resultado['error'] ?? 'No especificado') . "\n", 3, $logFile);
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
    <style>
        body { font-family: Arial, sans-serif; }
        form { max-width: 300px; margin: 20px auto; }
        label { display: block; margin: 5px 0; }
        input { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px; background-color: #007BFF; color: white; border: none; }
        button:hover { background-color: #0056b3; }

        /* Estilo para el modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .close-btn {
            margin-top: 20px;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Registro de Usuario</h1>

    <form action="" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <label for="confirmar_contrasena">Confirmar Contraseña:</label>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

        <button type="submit">Registrar</button>
    </form>

    <!-- Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <p><?php echo $message; ?></p>
            <button class="close-btn" onclick="closeModal()">Aceptar</button>
        </div>
    </div>

    <script>
        // Mostrar el modal si la variable $show_modal es verdadera
        <?php if ($show_modal): ?>
            document.getElementById('errorModal').style.display = 'flex';
        <?php endif; ?>

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
            window.history.back(); // Vuelve al formulario para rellenar de nuevo
        }
    </script>
</body>
</html>
