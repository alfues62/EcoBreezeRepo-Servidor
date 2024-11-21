<?php

function obtenerMedicionesUsuario($usuario_id) {
    // URL de la API para obtener mediciones
    $url = 'http://host.docker.internal:8080/api/api_datos.php?action=obtener_mediciones_usuario';

    $data = json_encode(['usuario_id' => $usuario_id]);

    // Hacer la solicitud CURL
    $result = hacerSolicitudCurl($url, $data);

    if ($result && isset($result['success']) && $result['success']) {
        return $result['mediciones'];
    } else {
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError($error_message);
        return ['error' => $error_message];
    }   
}
?>
