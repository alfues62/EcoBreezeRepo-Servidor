// Inicializar el mapa
var map = L.map('map').setView([39.4699, -0.3763], 13); // Coordenadas de Valencia, España

// Añadir una capa de mapa base
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Obtener las mediciones del mapa desde PHP
var mapaMediciones = window.mapaMediciones;

// Crear un array para los puntos del mapa de calor
var heatData = [];

// Crear un elemento de selección para filtrar por tipo de gas
var gasSelector = L.control({ position: 'topright' });
gasSelector.onAdd = function () {
    var div = L.DomUtil.create('div', 'info');
    div.innerHTML = '<select id="gasType">' +
        '<option value="">Seleccione un tipo de gas</option>' +
        '<option value="2">Ozono</option>' +
        '<option value="3">Monóxido de carbono</option>' +
        '<option value="4">Dióxido de nitrógeno</option>' +
        '<option value="5">Dióxido de azufre</option>' +
        '</select>';
    div.firstChild.onmousedown = div.firstChild.ondblclick = L.DomEvent.stopPropagation;
    return div;
};
gasSelector.addTo(map);

// Función para actualizar el mapa de calor
function updateHeatMap(gasType) {
    heatData = [];

    // Filtrar las mediciones según el tipo de gas seleccionado
    mapaMediciones.forEach(function(medicion) {
        if (gasType === '' || medicion.TIPOGAS_TipoID == gasType) {
            var lat = medicion.Lat;
            var lon = medicion.Lon;
            var valor = medicion.Valor; // El valor que utilizarás para la intensidad del calor

            // Filtrar valores para no incluir mediciones irrelevantes
            if (valor > 0) {  // Ajusta esta condición si lo necesitas
                // Agregar la coordenada y la intensidad (valor) al array heatData
                heatData.push([lat, lon, valor]);
            }
        }
    });

    // Limpiar el mapa de calor existente
    if (window.heatLayer) {
        map.removeLayer(window.heatLayer);
    }

    // Crear el mapa de calor usando los datos filtrados
    window.heatLayer = L.heatLayer(heatData, {
        radius: 25,  // Radio de los puntos de calor
        blur: 15,    // Desenfoque
        maxZoom: 13, // Nivel de zoom máximo
        minOpacity: 0.5,  // Opacidad mínima
        gradient: {
            0.2: 'blue',   // Bajos niveles
            0.5: 'lime',   // Niveles medios
            0.8: 'orange', // Niveles altos
            1.0: 'red'     // Valores máximos
        },
        max: Math.max(...mapaMediciones.map(m => m.Valor)) // Calcula el valor máximo de las mediciones
    }).addTo(map);
}

// Actualizar el mapa de calor al cambiar el tipo de gas
var gasTypeSelector = document.getElementById('gasType');
gasTypeSelector.addEventListener('change', function() {
    updateHeatMap(this.value);
});

// Inicializar el mapa con todos los datos
updateHeatMap('');