<?php

// Configuración de las URLs
$urls = [
    'https://api.waqi.info/feed/geo:39.4699;-0.3763/?token=96e288e9e984227009bec24173e837b4452b4036',
    'https://api.waqi.info/feed/here/?token=71a3d06d46f8ed1a11c7fd975be00ac2d2a871fb',
    'https://api.waqi.info/feed/@6644/?token=71a3d06d46f8ed1a11c7fd975be00ac2d2a871fb',
    'https://api.waqi.info/feed/@10531/?token=71a3d06d46f8ed1a11c7fd975be00ac2d2a871fb',
    'https://api.waqi.info/feed/@6639/?token=71a3d06d46f8ed1a11c7fd975be00ac2d2a871fb',
    'https://api.waqi.info/feed/@6645/?token=71a3d06d46f8ed1a11c7fd975be00ac2d2a871fb'
];

// Función para procesar una URL y devolver los datos extraídos
// Función para procesar una URL y devolver los datos extraídos
function processUrl($url) {
    try {
        // Obtener el contenido JSON de la URL
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] !== 'ok') {
            // Si no está ok, retornamos un array vacío
            echo "Error al obtener datos de la URL: $url\n";
            return [];
        }

        // Extraer los datos de AQI y la localización
        $aqi = $data['data']['aqi'];  // Aquí está el valor de AQI
        list($latitude, $longitude) = $data['data']['city']['geo'] ?? [null, null];
        $time = $data['data']['time']['iso'];  // Obtenemos la fecha y hora

        // Extraer los valores de los gases usando las equivalencias proporcionadas
        $co2 = $data['data']['iaqi']['co']['v'] ?? null;   // CO₂
        $no2 = $data['data']['iaqi']['no2']['v'] ?? null;   // NO₂
        $o3 = $data['data']['iaqi']['o3']['v'] ?? null;    // O₃
        $so2 = $data['data']['iaqi']['so2']['v'] ?? null;  // SO₂

        // Crear un array con los datos que queremos insertar
        $airData = [
            'time' => $time,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'ValorAQI' => $aqi,  // Guardamos el valor de AQI
            'CO2' => $co2,       // Guardamos el valor de CO₂
            'NO2' => $no2,       // Guardamos el valor de NO₂
            'O3' => $o3,         // Guardamos el valor de O₃
            'SO2' => $so2,       // Guardamos el valor de SO₂
        ];

        return [$airData];  // Retornamos los datos en un array

    } catch (Exception $e) {
        echo "Error procesando la URL $url: " . $e->getMessage() . "\n";
        return [];
    }
}

// Función para hacer una solicitud cURL a una API
function hacerSolicitudCurl($url, $data) {
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Ejecuta la solicitud y captura la respuesta
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }
        curl_close($ch);

        // Decodifica la respuesta JSON
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
        }

        return $result;

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Función principal para recorrer todas las URLs y procesarlas
function fetchAirDataAndSend($urls) {
    $allAirData = []; // Array global para almacenar todos los datos

    foreach ($urls as $url) {
        $airData = processUrl($url);
        $allAirData = array_merge($allAirData, $airData); // Agregar los datos al array general
    }

    // Si se han obtenido datos, los enviamos a la API
    if (!empty($allAirData)) {
        $apiUrl = 'http://host.docker.internal:8080/api/api_datos.php?action=insertar_mediciones';

        // Preparar los datos para enviar a la API (aquí puedes incluir más detalles si es necesario)
        $data = json_encode([
            'mediciones' => $allAirData,  // Los datos de medición que queremos enviar
        ]);

        // Enviar los datos usando cURL
        $result = hacerSolicitudCurl($apiUrl, $data);

        // Mostrar la respuesta del servidor
        echo "Respuesta de la API: " . print_r($result, true);
    } else {
        echo "No se encontraron datos de calidad del aire para enviar.";
    }
}

// Llamar a la función principal
fetchAirDataAndSend($urls);
