<?php

function obtenerUltimaMedicion() {
    $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=obtener_ultima_medicion';

    // Hacer la solicitud con cURL
    $result = hacerSolicitudCurl($url, json_encode([])); // Si la API espera datos vacíos

    if ($result && isset($result['success']) && $result['success']) {
        return $result;  // Asumiendo que los usuarios y sus mediciones están en 'usuarios'
    } else {
        $error_message = $result['error'] ?? 'Error desconocido.';
        registrarError($error_message); // Registra el error
        return ['error' => $error_message];
    }
}

?>