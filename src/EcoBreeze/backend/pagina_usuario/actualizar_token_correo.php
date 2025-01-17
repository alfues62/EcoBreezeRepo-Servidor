<?php

// Función para cambiar el correo
function cambiarToken($id, $contrasenaActual, $nuevoCorreo, $token) {
    
    $error_message = '';

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_token';
    $data = json_encode([
        'id' => $id,
        'contrasena_actual' => $contrasenaActual,
        'nuevo_correo' => $nuevoCorreo,
        'token' => $token
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        return ['success' => 'Por favor, verifique su nuevo correo.'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
