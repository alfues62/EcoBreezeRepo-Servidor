document.addEventListener("DOMContentLoaded", function () {
    console.log("El script de JavaScript ha sido cargado correctamente.");

    // Crear el mapa en el contenedor con ID 'map'
    const map = L.map("map").setView([39.4699, -0.3763], 10); // Vista inicial en Valencia

    // Añadir un mapa base (OpenStreetMap)
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 18,
        attribution: "© OpenStreetMap contributors"
    }).addTo(map);

    // Función para determinar el color según el valor medido
    function getColor(value) {
        if (value <= 50) return "green";
        if (value <= 100) return "yellow";
        return "red";
    }

    // Cargar las mediciones desde PHP
    fetch("../backend/mediciones_api/obtener_medicionesAPI.php")
        .then((response) => response.json())
        .then((mediciones) => {
            console.log("Datos obtenidos:", mediciones);

            mediciones.forEach((medicion) => {
                const { Lat, Lon, Valor, Fecha, Hora, TIPOGAS_TipoID } = medicion;

                // Convertir Lat y Lon a números
                const lat = parseFloat(Lat);
                const lon = parseFloat(Lon);

                if (isNaN(lat) || isNaN(lon)) {
                    console.error("Coordenadas inválidas:", { Lat, Lon });
                    return;
                }

                // Determinar el color del marcador
                const color = getColor(Valor);

                // Crear marcador con círculo
                const marker = L.circleMarker([lat, lon], {
                    color: color,
                    radius: 10,
                    fillColor: color,
                    fillOpacity: 0.7
                }).addTo(map);

                // Añadir un popup con detalles
                marker.bindPopup(`
                    <strong>Detalles de la medición:</strong><br>
                    Fecha: ${Fecha}<br>
                    Hora: ${Hora}<br>
                    Valor: ${Valor}<br>
                    Tipo de Gas: ${TIPOGAS_TipoID}<br>
                    Latitud: ${lat}, Longitud: ${lon}
                `);
            });
        })
        .catch((error) => {
            console.error("Error al cargar las mediciones:", error);
        });

    // Crear la leyenda
    const legend = L.control({ position: "bottomright" });

    legend.onAdd = function () {
        const div = L.DomUtil.create("div", "legend");
        div.innerHTML = `
            <strong>Calidad del Aire</strong><br>
            <i style="background: green"></i> Buena (≤50)<br>
            <i style="background: yellow"></i> Moderada (≤100)<br>
            <i style="background: red"></i> Mala (>100)
        `;
        return div;
    };

    legend.addTo(map);
});
