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

// Crear un elemento de selección para filtrar por fecha
var dateSelector = L.control({ position: 'topright' });
dateSelector.onAdd = function () {
    var div = L.DomUtil.create('div', 'info');
    var today = new Date().toISOString().split('T')[0]; // Fecha de hoy en formato yyyy-mm-dd
    div.innerHTML = '<input type="date" id="dateFilter" value="' + today + '" max="' + today + '" />';
    div.firstChild.onmousedown = div.firstChild.ondblclick = L.DomEvent.stopPropagation;
    return div;
};
dateSelector.addTo(map);

// Función para actualizar el mapa de calor con filtro de fecha y tipo de gas
function updateHeatMap(gasType, selectedDate) {
    heatData = [];

    // Limpiar los marcadores actuales
    if (window.markerLayer) {
        map.removeLayer(window.markerLayer);
    }

    // Crear una capa para los marcadores
    window.markerLayer = L.layerGroup().addTo(map);

    // Filtrar las mediciones según el tipo de gas y la fecha seleccionada
    mapaMediciones.forEach(function(medicion) {
        var medicionDate = medicion.Fecha; // Asegúrate de que el campo de fecha esté en formato yyyy-mm-dd
        if (
            (gasType === '' || medicion.TIPOGAS_TipoID == gasType) && // Filtrar por tipo de gas
            (!selectedDate || medicionDate === selectedDate) // Filtrar por fecha
        ) {
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
        maxZoom: 1, // Nivel de zoom máximo
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

// Obtener el elemento del filtro de tipo de gas
var gasTypeSelector = document.getElementById('gasType');

// Actualizar el mapa cuando se cambie el tipo de gas
gasTypeSelector.addEventListener('change', function() {
    var selectedDate = document.getElementById('dateFilter').value; // Obtener la fecha seleccionada
    updateHeatMap(this.value, selectedDate); // Actualizar el mapa con el gas y la fecha seleccionados
});

// Obtener el elemento del filtro de fecha
var dateFilter = document.getElementById('dateFilter');

// Escuchar cambios en la selección de la fecha
dateFilter.addEventListener('change', function() {
    var selectedDate = this.value;
    var gasType = gasTypeSelector.value; // Obtener el tipo de gas seleccionado
    updateHeatMap(gasType, selectedDate); // Actualizar el mapa con la fecha seleccionada
});

// Inicializar el mapa de calor con todos los datos (fecha predeterminada: hoy)
var today = new Date().toISOString().split('T')[0]; // Fecha de hoy en formato yyyy-mm-dd
updateHeatMap('', today); // Utilizar la fecha de hoy por defecto
