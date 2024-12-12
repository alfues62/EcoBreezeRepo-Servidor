<?php

function cambiarCorreo($email, $nuevoCorreo) {
    
    $error_message = '';

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_correo';
    $data = json_encode([
        'email' => $email,
        'nuevo_correo' => $nuevoCorreo
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        return ['success' => 'Correo cambiado con éxito'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
