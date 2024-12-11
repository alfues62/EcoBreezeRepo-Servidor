<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';

function obtenerMediciones() {

    // URL de la API que devuelve las mediciones
    $url = 'http://host.docker.internal:8080/api/api_mediciones.php?action=obtener_mediciones';

    // Realizar la solicitud cURL para obtener las mediciones
    $result = hacerSolicitudCurl($url, json_encode([]));

    // Verificar si la respuesta tiene éxito
    if (isset($result['success']) && $result['success']) {
        // Si es exitoso, devolver las mediciones
        return $result;
    } else {
        // Si no es exitoso, devolver el mensaje de error
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        registrarError("ERROR MEDICIONESSSSSSSSSSSSS" .$error_message);
        return ['error' => $error_message];
    }
}


// Obtener las mediciones
$mediciones = obtenerMediciones();

// Verificar si se obtuvieron mediciones
if (isset($mediciones['error'])) {
    echo "JOOOOOOODEEEEEEEER: " . $mediciones['error'];
} else {
    // Mostrar solo las mediciones
    echo '<pre>';
    print_r($mediciones);
    echo '</pre>';
}
?>
