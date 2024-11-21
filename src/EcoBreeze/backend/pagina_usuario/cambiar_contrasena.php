<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';  

// Función para cambiar la contraseña
function cambiarContrasena($id, $contrasenaActual, $nuevaContrasena) {
    
    $error_message = '';

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_contrasena';
    $data = json_encode([
        'id' => $id,
        'contrasena_actual' => $contrasenaActual,
        'nueva_contrasena' => $nuevaContrasena
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        return ['success' => 'Contraseña cambiada con éxito'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
