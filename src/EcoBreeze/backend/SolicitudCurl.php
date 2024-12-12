<?php
/**
 * Realiza una solicitud CURL a una API externa.
 *
 * Esta función realiza una solicitud POST utilizando CURL a una URL específica, enviando datos en formato JSON.
 * Puedes modificar la URL de destino y los datos que se envían según los requerimientos de la API.
 * En caso de error, se captura la excepción y se devuelve el mensaje de error.
 * 
 * Diseño:
 *
 * Entrada:
 *   - $url (string): La URL de la API a la que se enviará la solicitud.
 *   - $data (string): Los datos en formato JSON que se enviarán en la solicitud.
 *
 * Proceso:
 *   1. Inicia una nueva sesión CURL con la URL proporcionada.
 *   2. Configura las opciones necesarias para realizar una solicitud POST y enviar los datos.
 *   3. Ejecuta la solicitud y captura la respuesta de la API.
 *   4. Si hay algún error durante la ejecución de CURL, se lanza una excepción.
 *   5. Decodifica la respuesta JSON de la API y devuelve el resultado. Si hay un error en la decodificación, se lanza una excepción.
 *   6. En caso de error, se captura la excepción y se devuelve un mensaje de error.
 *
 * Salida:
 *   - Un array con los resultados de la solicitud si es exitosa.
 *   - Un array con un mensaje de error si ocurre algún problema.
 *
 * @param string $url La URL de la API a la que se enviará la solicitud.
 * @param string $data Los datos en formato JSON que se enviarán en la solicitud.
 * @return array El resultado de la solicitud o un mensaje de error en caso de fallo.
 */

function hacerSolicitudCurl($url, $data) {
    try {
        // Inicia una nueva sesión CURL con la URL proporcionada
        $ch = curl_init($url);

        // Establece opciones para la solicitud CURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna la respuesta como una cadena
        curl_setopt($ch, CURLOPT_POST, true); // Indica que es una solicitud POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Define que el contenido es JSON
            'Content-Length: ' . strlen($data) // Define el tamaño del contenido
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Establece los datos a enviar

        // Ejecuta la solicitud y captura la respuesta
        $response = curl_exec($ch);
        
        // Verifica si hubo algún error en la ejecución de CURL
        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }

        // Cierra la sesión CURL
        curl_close($ch);

        // Decodifica la respuesta JSON
        $result = json_decode($response, true);
        
        // Verifica si hubo algún error al decodificar el JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        // Devuelve el resultado de la solicitud
        return $result;

    } catch (Exception $e) {
        // Captura el error y lo devuelve en un array
        return ['error' => $e->getMessage()];
    }
}
?>
