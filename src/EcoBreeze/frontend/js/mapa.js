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

// Definir el valor máximo de intensidad
var maxIntensity = 0;

// Recorrer las mediciones para agregar datos al mapa de calor
mapaMediciones.forEach(function(medicion) {
    var lat = medicion.Lat;
    var lon = medicion.Lon;
    var valor = medicion.Valor; // El valor que utilizarás para la intensidad del calor

    // Filtrar valores para no incluir mediciones irrelevantes
    if (valor > 0) {  // Puedes ajustar esta condición según tus datos
        // Agregar la coordenada y la intensidad (valor) al array heatData
        heatData.push([lat, lon, valor]);

        // Encontrar el valor máximo de intensidad
        if (valor > maxIntensity) {
            maxIntensity = valor;
        }
    }
});

// Crear el mapa de calor usando los datos de las mediciones
L.heatLayer(heatData, {
    radius: 25,  // Radio de los puntos de calor
    blur: 15,    // Desenfoque
    maxZoom: 13, // Nivel de zoom máximo
    minOpacity: 0.5,  // Opacidad mínima
    max: maxIntensity  // Usar el valor máximo encontrado para la intensidad
}).addTo(map);
