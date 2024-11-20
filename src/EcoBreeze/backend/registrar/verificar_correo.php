<?php

require_once '../SolicitudCurl.php';

// Obtener el correo y el token desde la URL
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

// Realizar la solicitud para verificar el correo
$url = 'http://host.docker.internal:8080/api/api_usuario.php?action=verificar_correo';
$data = json_encode([
    'email' => $email,
    'token' => $token
]);

// Realizar la solicitud HTTP
$result = hacerSolicitudCurl($url, $data);

// Determinar el mensaje de éxito o error directamente desde la API
$message = '';
$error = '';

if (isset($result['success']) && $result['success']) {
    // Mostrar solo el mensaje de éxito devuelto por la API
    $message = htmlspecialchars($result['message']);
} else {
    // Mostrar solo el mensaje de error devuelto por la API
    $error = htmlspecialchars($result['error']);
    registrarError($error);
}

include '../../frontend/php/verificar_token.vista.php'
?>
