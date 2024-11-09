<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';  

function registrarUsuario($nombre, $apellidos, $email, $contrasena) {

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=registrar';
    $data = json_encode([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'email' => $email,
        'contrasena' => $contrasena,
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        return 'Usuario registrado con éxito.';
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
