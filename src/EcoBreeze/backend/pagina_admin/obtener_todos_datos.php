<?php

function obtenerMediciones() {
    // URL de la API para obtener mediciones
    $url = 'http://host.docker.internal:8080/api/api_datos.php?action=obtener_mediciones_todos_usuarios';

    // No necesitas enviar datos si solo estás obteniendo la información
    // Convierte el array vacío a JSON, que es una cadena de texto válida
    $data = json_encode([]);  // Convertimos el array vacío en JSON

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
