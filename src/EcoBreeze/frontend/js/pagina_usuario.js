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

    // Función para actualizar la gráfica
    const actualizarGrafica = (fechaSeleccionada) => {
        console.log('Filtrando mediciones para la fecha:', fechaSeleccionada);

        const medicionesFiltradas = mediciones.filter(m => {
            const medicionFecha = m.Fecha; // Fecha en formato "YYYY-MM-DD"
            const isSameDay = medicionFecha === fechaSeleccionada; // Comparar con la fecha seleccionada
            return m.TIPOGAS_TipoID === "2" && isSameDay;
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
            document.getElementById('error-message').innerText = `No hay mediciones de Ozono para la fecha seleccionada: ${fechaSeleccionada}.`;
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
                    label: `Mediciones de Ozono (${fechaSeleccionada})`, // Título de la serie de datos
                    data: dataValues, // Los valores de las mediciones
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo de la línea
                    borderColor: 'rgba(75, 192, 192, 1)', // Color de la línea
                    borderWidth: 2 // Grosor de la línea
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

    // Añadir un selector de fecha y un botón para aplicar el filtro
    const fechaSelector = document.createElement('input');
    fechaSelector.type = 'date';
    fechaSelector.id = 'fechaSelector';

    const filtrarFechaBtn = document.createElement('button');
    filtrarFechaBtn.id = 'filtrarFechaBtn';
    filtrarFechaBtn.textContent = 'Filtrar';

    medicionesContainer.insertAdjacentElement('beforebegin', fechaSelector);
    medicionesContainer.insertAdjacentElement('beforebegin', filtrarFechaBtn);

    // Manejar el evento de clic en el botón de filtro
    filtrarFechaBtn.addEventListener('click', () => {
        const fechaSeleccionada = fechaSelector.value;
        if (!fechaSeleccionada) {
            document.getElementById('error-message').innerText = 'Por favor, selecciona una fecha.';
            return;
        }
        actualizarGrafica(fechaSeleccionada);
    });

    // Inicializar con la fecha de hoy
    const today = new Date().toISOString().split('T')[0];
    fechaSelector.value = today; // Preseleccionar la fecha de hoy en el input
    actualizarGrafica(today); // Mostrar las mediciones de hoy al cargar la página
});
