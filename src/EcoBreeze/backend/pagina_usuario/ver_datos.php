<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';  

function obtenerDatosUsuario($id) {

    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=obtener_datos_usuario';
    $data = json_encode(['id' => $id]);

    $result = hacerSolicitudCurl($url, $data);

    if ($result && isset($result['success']) && $result['success']) {
        return $result['usuario'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }    
}