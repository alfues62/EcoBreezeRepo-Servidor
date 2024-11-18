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
    console.log('Mediciones recibidas:', mediciones);  // Depurar: ver las mediciones originales

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
        document.getElementById('error-message').innerText = 'No hay mediciones disponibles para graficar.';  // Mostrar mensaje en la UI
        return;
    }

    // Filtrar las mediciones de Ozono (TIPOGAS_TipoID === 2) y la fecha de hoy
    const today = new Date().toISOString().split('T')[0];  // Fecha actual en formato YYYY-MM-DD
    console.log('Fecha de hoy:', today); // Depurar: ver la fecha de hoy

    // Filtrando las mediciones por fecha y tipo de gas (Ozono)
    const medicionesFiltradas = mediciones.filter(m => {
        const medicionFecha = m.Fecha; // Fecha en formato "YYYY-MM-DD"
        const isSameDay = medicionFecha === today;  // Comparar la fecha de la medición con la fecha de hoy
        console.log(`Comparando fecha: ${medicionFecha} con hoy: ${today} -> ${isSameDay}`);
        return m.TIPOGAS_TipoID === "2" && isSameDay;
    });

    console.log('Mediciones filtradas:', medicionesFiltradas);  // Depurar: ver las mediciones filtradas

    // Si no hay mediciones filtradas, mostrar el mensaje en la UI y salir de la función
    if (medicionesFiltradas.length === 0) {
        document.getElementById('error-message').innerText = 'No hay mediciones de Ozono para el día de hoy.';  // Mostrar mensaje en la UI
        return;
    }

    // Preparar las etiquetas (labels) y los valores de la gráfica
    const labels = medicionesFiltradas.map(m => `${m.Fecha} ${m.Hora}`);
    const dataValues = medicionesFiltradas.map(m => parseFloat(m.Valor));

    console.log('Etiquetas:', labels);
    console.log('Valores:', dataValues);

    // Crear la gráfica con Chart.js
    const ctx = document.getElementById('graficaMediciones').getContext('2d');
    new Chart(ctx, {
        type: 'line',  // Tipo de gráfico
        data: {
            labels: labels,  // Etiquetas de las mediciones (Fecha y Hora)
            datasets: [{
                label: 'Mediciones de Ozono',  // Título de la serie de datos
                data: dataValues,  // Los valores de las mediciones
                backgroundColor: 'rgba(75, 192, 192, 0.2)',  // Color de fondo de la línea
                borderColor: 'rgba(75, 192, 192, 1)',  // Color de la línea
                borderWidth: 2  // Grosor de la línea
            }]
        },
        options: {
            responsive: true,  // Hace que el gráfico sea responsivo al tamaño de la pantalla
            maintainAspectRatio: false,  // No mantiene la relación de aspecto
            scales: {
                x: {
                    title: { display: true, text: 'Fecha y Hora' }  // Título del eje X
                },
                y: {
                    title: { display: true, text: 'Valor' },  // Título del eje Y
                    beginAtZero: true  // Asegura que el eje Y comience en 0
                }
            }
        }
    });
});
