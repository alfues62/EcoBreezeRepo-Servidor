<?php
session_start();

// Verificamos si el usuario está logueado, de lo contrario lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

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

    $id = $_SESSION['usuario_id'] ?? null;
    $contrasenaActual = $_POST['contrasena_actual'] ?? null;
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? null;
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? null;

    // Validamos y procesamos el cambio de contraseña
    if ($nuevaContrasena && $id && $nuevaContrasena === $confirmarContrasena) {
        $data = [
            'action' => 'cambiar_contrasena',
            'id' => $id,
            'contrasena_actual' => $contrasenaActual,
            'nueva_contrasena' => $nuevaContrasena
        ];

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('CURL Error: ' . curl_error($ch));
            }
            curl_close($ch);

            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            if (isset($result['success']) && $result['success']) {
                $cambio_exitoso = true;
                $success_message = 'Contraseña cambiada con éxito.';
            } else {
                $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            }

        } catch (Exception $e) {
            // Registra el error
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "{$timestamp} - Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $error_message = 'Ocurrió un error en el servidor. Inténtalo más tarde.';
        }
    } elseif ($nuevaContrasena && $id) {
        $error_message = 'Las nuevas contraseñas no coinciden.';
    }
}

// Obtener los datos del usuario si está autenticado
if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=obtener_datos_usuario';
    $data = ['id' => $id];

    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }

        curl_close($ch);
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        if (isset($result['success']) && $result['success']) {
            $usuario = $result['usuario'];
        } else {
            $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        }

    } catch (Exception $e) {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents('/var/www/html/logs/app.log', "{$timestamp} - Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        $error_message = 'Ocurrió un error en el servidor. Inténtalo más tarde.';
    }
} else {
    $error_message = 'No estás autenticado. Por favor, inicia sesión.';
}

include '../frontend/php/pagina_usuario.vista.php'
?>
