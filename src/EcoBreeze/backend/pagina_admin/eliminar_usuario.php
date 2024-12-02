<?php

// Función para eliminar un usuario
function eliminarUsuario($id) {
    
    $error_message = '';

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=eliminar_usuario';
    $data = json_encode([
        'id' => $id
    ]);

    $result = hacerSolicitudCurl($url, $data);

    if (isset($result['success']) && $result['success']) {
        return ['success' => 'Usuario eliminado con éxito'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
