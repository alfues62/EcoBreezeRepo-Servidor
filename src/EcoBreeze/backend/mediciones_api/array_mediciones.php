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

        // Extraer los datos
        $time = $data['data']['time']['iso'];
        list($latitude, $longitude) = $data['data']['city']['geo'] ?? [null, null];
        $iaqi = $data['data']['iaqi'];

        // Crear un array de datos procesados, solo para los tipos específicos
        $airData = [];
        foreach ($iaqi as $type => $valueData) {
            // Filtrar solo los tipos de gas que necesitamos
            if (in_array(strtoupper($type), ['O3', 'CO', 'NO2', 'SO4'])) {
                $airData[] = [
                    'time' => $time,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'tipoGasId' => getTipoGasId($type), // Mapeamos el tipo de gas a su TipoID
                    'value' => $valueData['v'],
                ];
            }
        }

        return $airData;
    } catch (Exception $e) {
        echo "Error procesando la URL $url: " . $e->getMessage() . "\n";
        return [];
    }
}

// Función para obtener el TipoID (simulamos que existe esta relación)
function getTipoGasId($type) {
    // Aquí asignamos los valores de TipoID según el tipo de gas
    $gasTypes = [
        'O3' => 2, // O3 -> TipoID = 1
        'CO' => 3, // CO -> TipoID = 2
        'NO2' => 4, // NO2 -> TipoID = 3
        'SO4' => 5, // SO4 -> TipoID = 4
    ];

    return $gasTypes[strtoupper($type)] ?? null; // Devuelve el TipoID o null si no se encuentra
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

?>