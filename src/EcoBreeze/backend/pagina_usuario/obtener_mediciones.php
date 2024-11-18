<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';

function obtenerMedicionesUsuario($usuario_id) {
    // URL de la API para obtener mediciones
    $url = 'http://host.docker.internal:8080/api/api_datos.php?action=obtener_mediciones_usuario';

    $data = json_encode(['usuario_id' => $usuario_id]);

    // Hacer la solicitud CURL
    $result = hacerSolicitudCurl($url, $data);

    // Verifica si la respuesta es válida (si la respuesta no es null o vacía)
    if ($result) {
        // Aquí ya no necesitamos json_decode si la API ya te da un array PHP
        if (is_array($result)) { // Si la respuesta es ya un array PHP
            // Devolver el resultado tal cual
            return $result;
        } elseif (is_string($result)) {
            // Si la respuesta es una cadena JSON, la decodificamos
            $decodedResult = json_decode($result, true);
            
            // Verifica si hubo error en la decodificación
            if (json_last_error() === JSON_ERROR_NONE) {
                // Si la decodificación es exitosa, devolvemos el resultado
                return $decodedResult;
            } else {
                // Si hubo error al decodificar
                $error_message = 'Error al decodificar JSON: ' . json_last_error_msg();
                registrarError($error_message);
                return ['error' => $error_message];  // Devuelve el error
            }
        } else {
            // Si la respuesta no es un array ni una cadena JSON
            $error_message = 'Respuesta inesperada de la API';
            registrarError($error_message);
            return ['error' => $error_message];
        }
    } else {
        // Si la respuesta está vacía o nula
        $error_message = 'Respuesta vacía de la API';
        registrarError($error_message);
        return ['error' => $error_message];
    }
}
?>
