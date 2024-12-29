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
        const map = L.map('map').setView([39.62842074544928, -0.3761891236455961], 10); // Vista inicial en Valencia

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
    const { Lat, Lon, ValorAQI, Fecha, Hora } = medicion; // Asegúrate de que 'ValorAQI' esté correctamente asignado

    // Verifica si el valor es correcto
    console.log("Valor AQI: ", ValorAQI);

    // Determina el color según el valor de AQI
    const color = getColor(ValorAQI);

    // Crear un marcador en el mapa
    const marker = L.circleMarker([Lat, Lon], {
        color: color,
        radius: 10,
        fillColor: color,
        fillOpacity: 0.65
    }).addTo(map);

    // Crear el contenido del popup con una estructura más bonita
    const popupContent = `
        <div style="font-family: 'Arial', sans-serif;">
            <h4 style="margin: 0; color: black ;"><strong>Detalles de la medición</strong></h4>
            <p style="margin: 5px 0;"><i class="bi bi-calendar"></i> <strong>Fecha:</strong> ${Fecha}</p>
            <p style="margin: 5px 0;"><i class="bi bi-clock"></i> <strong>Hora:</strong> ${Hora}</p>
            <p style="margin: 5px 0;"><i class="bi bi-thermometer-half"></i> <strong>Valor AQI:</strong> <span style="color: ${color};">${ValorAQI}</span></p>
            <p style="margin: 5px 0;"><i class="bi bi-geo-alt"></i> <strong>Ubicación:</strong> ${Lat}, ${Lon}</p>
            <p style="margin: 5px 0;">El valor AQI de <strong>${ValorAQI}</strong> indica una <strong>${color === 'green' ? 'buena calidad' : (color === 'yellow' ? 'calidad moderada' : 'mala calidad')}</strong> del aire.</p>
        </div>
    `;

    // Asigna el popup con el contenido
    marker.bindPopup(popupContent);
});




        // Crear la leyenda con un degradado de color
        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
                <strong>Calidad del Aire</strong><br>
                <i style="background: green"></i> Buena ( ≤ 50 )<br>
                <i style="background: yellow"></i> Moderada ( ≤ 100 )<br>
                <i style="background: red"></i> Mala ( > 100 )
            `;
            return div;
        };
        legend.addTo(map);
    </script>
</body>
</html>