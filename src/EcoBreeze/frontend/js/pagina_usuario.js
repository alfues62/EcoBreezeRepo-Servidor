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
    console.log('Mediciones recibidas:', mediciones);

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

    if (mediciones.length === 0) {
        document.getElementById('error-message').innerText = 'No hay mediciones disponibles para graficar.';
        return;
    }

    const graficaCanvas = document.getElementById('graficaMediciones');
    let grafica;

    const coloresPorGas = {
        2: 'rgba(75, 192, 192, 0.5)', // O3 - verde agua
        3: 'rgba(255, 99, 132, 0.5)', // CO - rojo
        4: 'rgba(54, 162, 235, 0.5)', // NO2 - azul
        5: 'rgba(255, 206, 86, 0.5)'  // SO4 - amarillo
    };

    const bordesPorGas = {
        2: 'rgba(75, 192, 192, 1)', // O3
        3: 'rgba(255, 99, 132, 1)', // CO
        4: 'rgba(54, 162, 235, 1)', // NO2
        5: 'rgba(255, 206, 86, 1)'  // SO4
    };

    function determinarNivelPromedio(mediciones) {
        if (mediciones.length === 0) return 'No hay mediciones disponibles';

        const sumaValores = mediciones.reduce((acumulado, medicion) => acumulado + parseFloat(medicion.Valor), 0);
        const promedio = sumaValores / mediciones.length;

        const rangosPorGas = {
            2: { optimo: [0, 0.05], moderado: [0.051, 0.10], alto: [0.101, Infinity] }, // O3
            3: { optimo: [0, 9], moderado: [9.01, 35], alto: [35.01, Infinity] },      // CO
            4: { optimo: [0, 0.03], moderado: [0.031, 0.06], alto: [0.061, Infinity] }, // NO2
            5: { optimo: [0, 0.02], moderado: [0.021, 0.075], alto: [0.076, Infinity] } // SO4
        };

        const tipoGas = mediciones[0].TIPOGAS_TipoID;
        const rangos = rangosPorGas[tipoGas];

        if (!rangos) return 'Tipo de gas desconocido';

        if (promedio >= rangos.optimo[0] && promedio <= rangos.optimo[1]) {
            return `Óptimo (Promedio: ${promedio.toFixed(3)} ppm)`;
        } else if (promedio >= rangos.moderado[0] && promedio <= rangos.moderado[1]) {
            return `Moderado (Promedio: ${promedio.toFixed(3)} ppm)`;
        } else if (promedio >= rangos.alto[0]) {
            return `Alto (Promedio: ${promedio.toFixed(3)} ppm)`;
        } else {
            return 'Nivel desconocido';
        }
    }

    const actualizarGrafica = (fechaSeleccionada, tipoGasSeleccionado) => {
        console.log('Filtrando mediciones para la fecha:', fechaSeleccionada, 'y tipo de gas:', tipoGasSeleccionado);

        const medicionesFiltradas = mediciones.filter(m => {
            const medicionFecha = m.Fecha;
            return m.TIPOGAS_TipoID === tipoGasSeleccionado && medicionFecha === fechaSeleccionada;
        });

        medicionesFiltradas.sort((a, b) => {
            const dateA = new Date(`${a.Fecha}T${a.Hora}`);
            const dateB = new Date(`${b.Fecha}T${b.Hora}`);
            return dateA - dateB;
        });

        if (medicionesFiltradas.length === 0) {
            document.getElementById('error-message').innerText = `No hay mediciones para el tipo de gas seleccionado (${tipoGasSeleccionado}) en la fecha: ${fechaSeleccionada}.`;
            if (grafica) grafica.destroy();
            return;
        }

        const nivelPromedio = determinarNivelPromedio(medicionesFiltradas);
        document.getElementById('nivelPromedio').innerText = `Nivel promedio del gas: ${nivelPromedio}`;

        const labels = medicionesFiltradas.map(m => `${m.Fecha} ${m.Hora}`);
        const dataValues = medicionesFiltradas.map(m => parseFloat(m.Valor));

        if (grafica) grafica.destroy();

        const ctx = graficaCanvas.getContext('2d');
        grafica = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: `Mediciones (${tipoGasSeleccionado}) - ${fechaSeleccionada}`,
                    data: dataValues,
                    backgroundColor: coloresPorGas[tipoGasSeleccionado],
                    borderColor: bordesPorGas[tipoGasSeleccionado],
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

        document.getElementById('error-message').innerText = '';
    };

    const fechaSelector = document.createElement('input');
    fechaSelector.type = 'date';
    fechaSelector.id = 'fechaSelector';

    const tipoGasSelector = document.createElement('select');
    tipoGasSelector.id = 'tipoGasSelector';
    tipoGasSelector.innerHTML = `
        <option value="2">O3</option>
        <option value="3">CO</option>
        <option value="4">NO2</option>
        <option value="5">SO4</option>
    `;

    const filtrarFechaBtn = document.createElement('button');
    filtrarFechaBtn.id = 'filtrarFechaBtn';
    filtrarFechaBtn.textContent = 'Filtrar';

    medicionesContainer.insertAdjacentElement('beforebegin', tipoGasSelector);
    medicionesContainer.insertAdjacentElement('beforebegin', fechaSelector);
    medicionesContainer.insertAdjacentElement('beforebegin', filtrarFechaBtn);

    const nivelPromedioContainer = document.createElement('p');
    nivelPromedioContainer.id = 'nivelPromedio';
    medicionesContainer.insertAdjacentElement('beforebegin', nivelPromedioContainer);

    filtrarFechaBtn.addEventListener('click', () => {
        const fechaSeleccionada = fechaSelector.value;
        const tipoGasSeleccionado = tipoGasSelector.value;

        if (!fechaSeleccionada) {
            document.getElementById('error-message').innerText = 'Por favor, selecciona una fecha.';
            return;
        }

        actualizarGrafica(fechaSeleccionada, tipoGasSeleccionado);
    });

    const today = new Date().toISOString().split('T')[0];
    fechaSelector.value = today;
    actualizarGrafica(today, '2');
});
