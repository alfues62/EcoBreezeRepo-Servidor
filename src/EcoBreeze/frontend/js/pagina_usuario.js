function redirectToSamePage() {
    window.location.href = window.location.href.split('?')[0]; // Redirigir a la misma página sin parámetros
}

document.addEventListener('DOMContentLoaded', function () {
    // Mostrar el modal de éxito si hay un mensaje de éxito
    const successMessage = document.getElementById('successMessage').innerText;
    if (successMessage.trim() !== '') {
        $('#successModal .modal-body').text(successMessage);
        $('#successModal').modal('show');
    }

    // Mostrar el modal de error si hay un mensaje de error
    const errorMessage = document.getElementById('errorMessage').innerText;
    if (errorMessage.trim() !== '') {
        $('#errorModal .modal-body').text(errorMessage);
        $('#errorModal').modal('show');
    }

    // Asegúrate de que las mediciones están correctamente asignadas desde el backend
    const mediciones = Array.isArray(window.mediciones) ? window.mediciones : [];
    console.log('Mediciones recibidas:', mediciones); // Depurar: ver las mediciones originales

    // Mostrar las mediciones por pantalla antes de graficar
    const medicionesContainer = document.getElementById('mediciones-container');
    if (mediciones.length > 0) {
        medicionesContainer.innerHTML = `
            <h3>Mediciones recibidas:</h3>
            <pre>${JSON.stringify(mediciones, null, 2)}</pre>
        `;
    } else {
        medicionesContainer.innerHTML = '<p>No se recibieron mediciones.</p>';
    }

    // Si no hay mediciones, mostrar el mensaje en la UI y salir de la función
    if (mediciones.length === 0) {
        document.getElementById('error-message').innerText = 'No hay mediciones disponibles para graficar.'; // Mostrar mensaje en la UI
        return;
    }

    // Crear referencia al canvas y definir la gráfica como variable global
    const graficaCanvas = document.getElementById('graficaMediciones');
    let grafica; // Variable para guardar la instancia del gráfico

    // Mapa de colores por tipo de gas
    const coloresPorGas = {
        4: 'rgba(75, 192, 192, 0.5)', // O3 - verde agua
        5: 'rgba(255, 99, 132, 0.5)', // CO - rojo
        6: 'rgba(54, 162, 235, 0.5)', // NO2 - azul
        7: 'rgba(255, 206, 86, 0.5)'  // SO4 - amarillo
    };

    // Mapa de colores para bordes (opacos)
    const bordesPorGas = {
        4: 'rgba(75, 192, 192, 1)', // O3
        5: 'rgba(255, 99, 132, 1)', // CO
        6: 'rgba(54, 162, 235, 1)', // NO2
        7: 'rgba(255, 206, 86, 1)'  // SO4
    };

    // Función para actualizar la gráfica
    const actualizarGrafica = (fechaSeleccionada, tipoGasSeleccionado) => {
        console.log('Filtrando mediciones para la fecha:', fechaSeleccionada, 'y tipo de gas:', tipoGasSeleccionado);

        const medicionesFiltradas = mediciones.filter(m => {
            const medicionFecha = m.Fecha; // Fecha en formato "YYYY-MM-DD"
            const isSameDay = medicionFecha === fechaSeleccionada; // Comparar con la fecha seleccionada
            return m.TIPOGAS_TipoID === tipoGasSeleccionado && isSameDay;
        });

        console.log('Mediciones filtradas antes de ordenar:', medicionesFiltradas);

        // Ordenar las mediciones por fecha y hora de forma ascendente (más antigua a más reciente)
        medicionesFiltradas.sort((a, b) => {
            const dateA = new Date(`${a.Fecha}T${a.Hora}`);
            const dateB = new Date(`${b.Fecha}T${b.Hora}`);
            return dateA - dateB; // Orden ascendente
        });

        console.log('Mediciones filtradas después de ordenar:', medicionesFiltradas);

        if (medicionesFiltradas.length === 0) {
            document.getElementById('error-message').innerText = `No hay mediciones para el tipo de gas seleccionado (${tipoGasSeleccionado}) en la fecha: ${fechaSeleccionada}.`;
            if (grafica) grafica.destroy(); // Destruye la gráfica actual si no hay datos
            return;
        }

        // Preparar las etiquetas (labels) y los valores de la gráfica
        const labels = medicionesFiltradas.map(m => `${m.Fecha} ${m.Hora}`);
        const dataValues = medicionesFiltradas.map(m => parseFloat(m.Valor));

        console.log('Etiquetas:', labels);
        console.log('Valores:', dataValues);

        // Si ya existe una gráfica, destrúyela antes de crear una nueva
        if (grafica) grafica.destroy();

        const ctx = graficaCanvas.getContext('2d');
        grafica = new Chart(ctx, {
            type: 'line', // Tipo de gráfico
            data: {
                labels: labels, // Etiquetas de las mediciones (Fecha y Hora)
                datasets: [{
                    label: `Mediciones (${tipoGasSeleccionado}) - ${fechaSeleccionada}`, // Título de la serie de datos
                    data: dataValues, // Los valores de las mediciones
                    backgroundColor: coloresPorGas[tipoGasSeleccionado], // Color asociado al gas
                    borderColor: bordesPorGas[tipoGasSeleccionado], // Color del borde asociado al gas
                    borderWidth: 2 // Grosor del borde
                }]
            },
            options: {
                responsive: true, // Hace que el gráfico sea responsivo al tamaño de la pantalla
                maintainAspectRatio: false, // No mantiene la relación de aspecto
                scales: {
                    x: {
                        title: { display: true, text: 'Fecha y Hora' } // Título del eje X
                    },
                    y: {
                        title: { display: true, text: 'Valor' }, // Título del eje Y
                        beginAtZero: true // Asegura que el eje Y comience en 0
                    }
                }
            }
        });

        document.getElementById('error-message').innerText = ''; // Limpiar errores si los hubo
    };

    // Añadir un selector de fecha
    const fechaSelector = document.createElement('input');
    fechaSelector.type = 'date';
    fechaSelector.id = 'fechaSelector';

    // Añadir un selector de tipo de gas
    const tipoGasSelector = document.createElement('select');
    tipoGasSelector.id = 'tipoGasSelector';
    tipoGasSelector.innerHTML = `
        <option value="4">O3</option>
        <option value="5">CO</option>
        <option value="6">NO2</option>
        <option value="7">SO4</option>
    `;

    const filtrarFechaBtn = document.createElement('button');
    filtrarFechaBtn.id = 'filtrarFechaBtn';
    filtrarFechaBtn.textContent = 'Filtrar';

    medicionesContainer.insertAdjacentElement('beforebegin', tipoGasSelector);
    medicionesContainer.insertAdjacentElement('beforebegin', fechaSelector);
    medicionesContainer.insertAdjacentElement('beforebegin', filtrarFechaBtn);

    // Manejar el evento de clic en el botón de filtro
    filtrarFechaBtn.addEventListener('click', () => {
        const fechaSeleccionada = fechaSelector.value;
        const tipoGasSeleccionado = tipoGasSelector.value;

        if (!fechaSeleccionada) {
            document.getElementById('error-message').innerText = 'Por favor, selecciona una fecha.';
            return;
        }

        actualizarGrafica(fechaSeleccionada, tipoGasSeleccionado);
    });

    // Inicializar con la fecha de hoy y el primer tipo de gas
    const today = new Date().toISOString().split('T')[0];
    fechaSelector.value = today; // Preseleccionar la fecha de hoy en el input
    actualizarGrafica(today, '4'); // Mostrar las mediciones de hoy para O3 al cargar la página
});
