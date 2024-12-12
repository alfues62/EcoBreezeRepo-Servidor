<?php
require_once '../log.php';
require_once '../SolicitudCurl.php';

function obtenerMediciones() {
    // URL de la API que devuelve las mediciones
    $url = 'http://host.docker.internal:8080/api/api_datos.php?action=obtener_mediciones';

    // Realizar la solicitud cURL para obtener las mediciones
    $result = hacerSolicitudCurl($url, json_encode([]));

    // Verificar si la respuesta tiene éxito
    if (isset($result['success']) && $result['success']) {
        // Si es exitoso, devolver las mediciones
        return $result['mediciones'];
    } else {
        // Si no es exitoso, devolver el mensaje de error
        $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
        return ['error' => $error_message];
    }
}

// Obtener las mediciones
$mediciones = obtenerMediciones();

// Verificar si se obtuvieron mediciones
if (isset($mediciones['error'])) {
    echo "Error: " . $mediciones['error'];
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Calidad del Aire</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/index.css">


</head>
<body>

    <!-- Navigation -->
    <nav class="navbar fixed-top">
        <div class="navbar-brand">
            <a href="/frontend/index.php">
                <img src="/frontend/img/logoBio.png" alt="Logo" class="logo">
            </a>
            <span>EcoBreeze</span>
        </div>
        <ul class="navbar-nav">
            <li><a href="/frontend/index.php" class="btn btn-volverMapa">VOLVER</a></li>

        </ul>
    </nav>
    <div class="contenedorMapa">
        <div id="map"></div>
    </div>
    <script>
        // Crear el mapa
        const map = L.map('map').setView([39.4699, -0.3763], 10); // Vista inicial en Valencia

        // Añadir un mapa base (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Datos de mediciones desde PHP
        const mediciones = <?php echo json_encode($mediciones); ?>;

        // Función para determinar el color según el valor medido (ajusta los umbrales según necesites)
        function getColor(value) {
            if (value <= 50) return 'green';
            if (value <= 100) return 'yellow';
            return 'red';
        }

        // Añadir marcadores al mapa
        mediciones.forEach(medicion => {
            const { Lat, Lon, Valor, Fecha, Hora, TIPOGAS_TipoID } = medicion;

            // Determinar el color del marcador según la calidad del aire
            const color = getColor(Valor);

            // Crear un marcador con un círculo
            const marker = L.circleMarker([Lat, Lon], {
                color: color,
                radius: 10,
                fillColor: color,
                fillOpacity: 0.7
            }).addTo(map);

            // Añadir un popup con los detalles de la medición
            marker.bindPopup(`
                <strong>Detalles de la medición:</strong><br>
                Fecha: ${Fecha}<br>
                Hora: ${Hora}<br>
                Valor: ${Valor}<br>
                Tipo de Gas: ${TIPOGAS_TipoID}<br>
                Latitud: ${Lat}, Longitud: ${Lon}
            `);
        });

        // Crear la leyenda
        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
                <strong>Calidad del Aire</strong><br>
                <i style="background: green"></i> Buena (≤50)<br>
                <i style="background: yellow"></i> Moderada (≤100)<br>
                <i style="background: red"></i> Mala (>100)
            `;
            return div;
        };

        legend.addTo(map);
    </script>
</body>
</html>
