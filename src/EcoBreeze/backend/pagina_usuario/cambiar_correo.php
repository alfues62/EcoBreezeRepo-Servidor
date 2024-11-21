<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';  

// Función para cambiar la contraseña
function cambiarCorreo($id, $contrasenaActual, $nuevoCorreo) {
    
    $error_message = '';

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_correo';
    $data = json_encode([
        'id' => $id,
        'contrasena_actual' => $contrasenaActual,
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
