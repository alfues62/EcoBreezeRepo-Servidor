<?php
session_start();

// Configura la zona horaria y el archivo de log
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';

// Redirige al usuario si ya está autenticado
if (isset($_SESSION['usuario_id'])) {
    header("Location: pagina_usuario.php");
    exit;
}

// Variables para los mensajes de error
$error_message = '';


// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mantén el parámetro 'action' en la URL
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion';  

    // Sanitiza y valida el email
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Correo electrónico no válido.';
    } else {
        $contrasena = trim($_POST['contrasena'] ?? '');

        // Datos a enviar en formato JSON
        $data = json_encode([
            'email' => $email,
            'contrasena' => $contrasena
        ]);

        // Realiza la solicitud POST
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);  // Usar POST en lugar de GET

            // Configura los encabezados para enviar los datos como JSON
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',  // Indica que se está enviando JSON
                'Content-Length: ' . strlen($data)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  // Enviar los datos en formato JSON

            // Ejecuta la solicitud
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('CURL Error: ' . curl_error($ch));
            }
            curl_close($ch);

            // Decodificar la respuesta JSON
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            // Maneja la respuesta de la API
            if (isset($result['success']) && $result['success']) {
                $_SESSION['usuario_id'] = $result['usuario']['ID']; // Asegúrate de que la API devuelve 'ID'
                $_SESSION['nombre'] = $result['usuario']['Nombre'];
                $_SESSION['rol'] = $result['usuario']['Rol'];

                // Redirige a dashboard
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
}

include '../frontend/php/login.vista.php';

?>
