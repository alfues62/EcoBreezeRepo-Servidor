document.addEventListener('DOMContentLoaded', function () {
    const mediciones = Array.isArray(window.mediciones) ? window.mediciones : [];
    console.log(mediciones); // Depurar: ver las mediciones originales

    // Si no hay mediciones, mostrar el mensaje en la UI y salir de la función
    if (mediciones.length === 0) {
        document.getElementById('error-message').innerText = 'No hay mediciones disponibles para graficar.';  // Mostrar mensaje en la UI
        return;
    }

    // Filtrar las mediciones de Ozono (TIPOGAS_TipoID === 2) y la fecha de hoy
    const today = new Date().toISOString().split('T')[0];
    console.log('Fecha de hoy:', today); // Depurar: ver la fecha de hoy

    const medicionesFiltradas = mediciones
        .filter(m => m.TIPOGAS_TipoID === 2 && m.Fecha.split(' ')[0] === today);

    console.log(medicionesFiltradas); // Depurar: ver las mediciones filtradas

    // Si no hay mediciones filtradas, mostrar el mensaje en la UI y salir de la función
    if (medicionesFiltradas.length === 0) {
        document.getElementById('error-message').innerText = 'No hay mediciones de Ozono para el día de hoy.';  // Mostrar mensaje en la UI
        return;
    }

    const labels = medicionesFiltradas.map(m => `${m.Fecha} ${m.Hora}`);
    const dataValues = medicionesFiltradas.map(m => m.Valor);

    const ctx = document.getElementById('graficaMediciones').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Mediciones de Ozono',
                data: dataValues,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: { display: true, text: 'Fecha y Hora' }
                },
                y: {
                    title: { display: true, text: 'Valor' },
                    beginAtZero: true
                }
            }
        }
    });
});
