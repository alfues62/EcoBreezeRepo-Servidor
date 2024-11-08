<?php
date_default_timezone_set('Europe/Madrid');
$logFile = '/var/www/html/logs/app.log';  // Archivo para registrar los errores

// Inicializamos las variables para los mensajes de éxito y error
$success_message = '';
$error_message = '';

// Comprobamos si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos los datos del formulario
    $nombre = filter_var(trim($_POST['nombre'] ?? ''), FILTER_SANITIZE_STRING);
    $apellidos = filter_var(trim($_POST['apellidos'] ?? ''), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $contrasena = trim($_POST['contrasena'] ?? '');
    $contrasena_confirmar = trim($_POST['contrasena_confirmar'] ?? '');

    // Validaciones
    if ($contrasena !== $contrasena_confirmar) {
        $error_message = 'Las contraseñas no coinciden.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
        $error_message = 'El nombre solo puede contener letras y espacios.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $apellidos)) {
        $error_message = 'Los apellidos solo pueden contener letras y espacios.';
    } 

    //Descomentar par contraseña compleja
    /*elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
        // Verificación de contraseña compleja
        $error_message = 'La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.';
    }*/ 
    
    
    else {
        // Si las validaciones son correctas, enviamos los datos a la API
        try {
            $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=registrar';

            // Preparamos los datos para la solicitud
            $data = json_encode([
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'contrasena' => $contrasena,
            ]);

            // Inicializamos la conexión cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            // Ejecutamos la solicitud cURL
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('Error en cURL: ' . curl_error($ch));
            }
            curl_close($ch);

            // Decodificamos la respuesta JSON
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            // Procesamos la respuesta de la API
            if (isset($result['success']) && $result['success']) {
                $success_message = htmlspecialchars($result['message'] ?? 'Usuario registrado con éxito.');
            } else {
                $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            }

        } catch (Exception $e) {
            // En caso de error, lo registramos y mostramos un mensaje genérico
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "{$timestamp} - Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $error_message = 'Ocurrió un error en el servidor. Inténtalo más tarde.';
        }
    }
}

include '../frontend/php/registro.vista.php';
?>
