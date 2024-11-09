<?php
require_once '../log.php';
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

// Determinar el mensaje de éxito o error
$message = '';
$error = '';

if (isset($result['success']) && $result['success']) {
    $message = '¡Correo verificado con éxito!';
} else {
    $error = htmlspecialchars($result['error'] ?? 'Error desconocido.');
    registrarError($error);
}

// Incluir la vista para mostrar el resultado
include '../../frontend/php/verificar_token.vista.php';
?>
