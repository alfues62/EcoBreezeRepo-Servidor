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

    // Configuración de escalas específicas para cada tipo de gas
    const escalasPorGas = {
        2: { min: 0, max: 0.3 },  // O3 (Ozono) - escala ampliada
        3: { min: 0, max: 50 },   // CO (Monóxido de carbono) - Ajustado para ppm
        4: { min: 0, max: 0.1 },  // NO2 (Dióxido de nitrógeno)
        5: { min: 0, max: 0.2 }   // SO4 (Sulfato)
    };

    const rangosPorGas = {
        2: { optimo: [0, 0.05], moderado: [0.051, 0.10], alto: [0.101, Infinity] }, // O3
        3: { optimo: [0, 9], moderado: [9.01, 35], alto: [35.01, Infinity] },      // CO
        4: { optimo: [0, 0.03], moderado: [0.031, 0.06], alto: [0.061, Infinity] }, // NO2
        5: { optimo: [0, 0.02], moderado: [0.021, 0.075], alto: [0.076, Infinity] } // SO4
    };

    // Función para determinar el nivel de gas basado en las mediciones
function determinarNivelPromedio(mediciones) {
    if (mediciones.length === 0) return 'No hay mediciones disponibles';

    const sumaValores = mediciones.reduce((acumulado, medicion) => acumulado + parseFloat(medicion.Valor), 0);
    const promedio = sumaValores / mediciones.length;

    const tipoGas = mediciones[0].TIPOGAS_TipoID;
    const rangos = rangosPorGas[tipoGas];

    if (!rangos) return 'Tipo de gas desconocido';

    if (promedio >= rangos.optimo[0] && promedio <= rangos.optimo[1]) {
        return `¡Todo está bien! El nivel de gas es seguro y adecuado para el ambiente.`;
    } else if (promedio >= rangos.moderado[0] && promedio <= rangos.moderado[1]) {
        return `¡Atento! Los niveles de gas estan empezando a elevarse, te recomendamos tomar precauciones.`;
    } else if (promedio >= rangos.alto[0]) {
        return `¡Cuidado! El nivel de gas está bastante alto, te recomendamos ir a un sitio seguro donde los niveles sean más bajos.`;
    } else {
        return 'Nivel desconocido';
    }
}

// Función para calcular los promedios de los gases filtrando solo por fecha
function calcularPromedios(mediciones, fechaSeleccionada) {
    const promedios = {};

    mediciones.forEach(medicion => {
        // Filtrar solo por fecha
        if (medicion.Fecha !== fechaSeleccionada) {
            return; // Si no coincide la fecha, no procesamos esta medición
        }

        // Asegurarse de que Valor sea un número
        const valor = parseFloat(medicion.Valor);
        const gas = medicion.TIPOGAS_TipoID;  // Usar TIPOGAS_TipoID como identificador del gas

        // Verificar que el valor sea un número válido
        if (isNaN(valor)) return;

        // Si el gas no existe en el objeto promedios, inicializarlo
        if (!promedios[gas]) {
            promedios[gas] = { total: 0, cantidad: 0 };
        }

        // Acumular el valor y la cantidad
        promedios[gas].total += valor;
        promedios[gas].cantidad += 1;
    });

    // Calcular el promedio final para cada gas
    for (const gas in promedios) {
        promedios[gas].promedio = promedios[gas].total / promedios[gas].cantidad;
    }

    console.log(promedios);  // Para verificar los resultados en consola
    return promedios;
}

// Función para actualizar la tabla con los promedios filtrados solo por fecha
function actualizarTabla(promedios) {
    const tabla = document.getElementById("tablaPromedios").getElementsByTagName('tbody')[0];
    tabla.innerHTML = '';  // Limpiar tabla

    for (const gas in promedios) {
        const fila = tabla.insertRow();
        const celdaGas = fila.insertCell(0);
        const celdaPromedio = fila.insertCell(1);

        // Aquí puedes usar una función para obtener el nombre del gas
        celdaGas.textContent = obtenerNombreGas(gas);
        celdaPromedio.textContent = promedios[gas].promedio.toFixed(2); // Mostrar promedio con dos decimales
    }
}

// Función para obtener el nombre del gas basado en TIPOGAS_TipoID
function obtenerNombreGas(tipoGasID) {
    const nombresGas = {
        "2": "O3 (Ozono)",
        "3": "CO (Monóxido de carbono)",
        "4": "NO2 (Dióxido de nitrógeno)",
        "5": "SO4 (Sulfato)"
    };

    return nombresGas[tipoGasID] || "Desconocido";
}




    
    const actualizarGrafica = (fechaSeleccionada, tipoGasSeleccionado) => {
        console.log('Filtrando mediciones para la fecha:', fechaSeleccionada, 'y tipo de gas:', tipoGasSeleccionado);

        // Filtrar las mediciones basadas en la fecha y tipo de gas seleccionados
        const medicionesFiltradas = mediciones.filter(m => {
            const medicionFecha = m.Fecha;
            return m.TIPOGAS_TipoID === tipoGasSeleccionado && medicionFecha === fechaSeleccionada;
        });
    
        medicionesFiltradas.sort((a, b) => {
            const dateA = new Date(`${a.Fecha}T${a.Hora}`);
            const dateB = new Date(`${b.Fecha}T${b.Hora}`);
            return dateA - dateB;
        });

        const nivelPromedio = determinarNivelPromedio(medicionesFiltradas);
        document.getElementById('nivelPromedio').innerText = nivelPromedio;
    
        const labels = medicionesFiltradas.map(m => `${m.Fecha} ${m.Hora}`);
        const dataValues = medicionesFiltradas.map(m => parseFloat(m.Valor));
    
        if (grafica) grafica.destroy();
    
        const ctx = graficaCanvas.getContext('2d');
        const escalaY = escalasPorGas[tipoGasSeleccionado] || { min: 0, max: 50 }; 
        
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
                        beginAtZero: true,
                        min: escalaY.min,
                        max: escalaY.max,
                        grid: {
                            color: function(context) {
                                const valor = context.tick.value; // Valor de la línea de la cuadrícula
                                const rangos = rangosPorGas[tipoGasSeleccionado];
    
                                if (!rangos) return 'rgba(0, 0, 0, 0.1)'; // Si no hay rangos, color predeterminado
    
                                // Comprobar límites de rangos y devolver colores
                                if (valor === rangos.optimo[1]) {
                                    return 'green'; // Límite superior del rango óptimo
                                }
                                if (valor === rangos.moderado[1]) {
                                    return 'yellow'; // Límite superior del rango moderado
                                }
                                if (valor === rangos.alto[0]) {
                                    return 'red'; // Límite inferior del rango alto
                                }
    
                                return 'rgba(0, 0, 0, 0.1)'; // Color predeterminado para otras líneas
                            },
                            lineWidth: function(context) {
                                const valor = context.tick.value; // Valor de la línea de la cuadrícula
                                const rangos = rangosPorGas[tipoGasSeleccionado];
    
                                if (!rangos) return 1; // Si no hay rangos, ancho predeterminado
    
                                // Aumentar el ancho para líneas de rangos
                                if (valor === rangos.optimo[1] || valor === rangos.moderado[1] || valor === rangos.alto[0]) {
                                    return 2; // Más grueso para líneas importantes
                                }
    
                                return 1; // Ancho predeterminado
                            }
                        }
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

    const medicionesContainer = document.getElementById('mediciones-container');
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
        // Filtramos y calculamos los promedios
        const promedios = calcularPromedios(mediciones, fechaSeleccionada);

        // Actualizamos la tabla con los promedios
        actualizarTabla(promedios);
    });

    const today = new Date().toISOString().split('T')[0];
    fechaSelector.value = today;

    actualizarGrafica(today, '2');


});

document.addEventListener('DOMContentLoaded', function () {
    // Función para alternar visibilidad de contraseñas
    function togglePasswordVisibility(inputId, toggleButton) {
        const passwordInput = document.getElementById(inputId);
        const icon = toggleButton.querySelector('svg');

        toggleButton.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Cambiar el icono entre ojo abierto y cerrado
            if (isPassword) {
                icon.innerHTML = `
                    <path fill-rule="evenodd" d="M2.258 12C3.79 7.558 7.818 4.5 12 4.5c4.182 0 8.21 3.058 9.742 7.5-1.532 4.442-5.56 7.5-9.742 7.5-4.182 0-8.21-3.058-9.742-7.5zm9.742-6a9.027 9.027 0 00-7.938 4.683c-.41.732-.41 1.902 0 2.634A9.027 9.027 0 0012 18c3.209 0 6.296-2.034 7.938-4.683.41-.732.41-1.902 0-2.634A9.027 9.027 0 0012 6zm0 7.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                `;
            } else {
                icon.innerHTML = `
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 011.414 0l16.586 16.586a1 1 0 01-1.414 1.414l-2.112-2.112a10.971 10.971 0 01-5.181 1.319c-4.182 0-8.21-3.058-9.742-7.5a10.947 10.947 0 012.746-4.076L3.707 3.707a1 1 0 010-1.414zM7.94 9.827l-2.16-2.16a9.03 9.03 0 00-1.32 2.661c-.41.732-.41 1.902 0 2.634A9.027 9.027 0 0012 18c1.582 0 3.08-.373 4.405-1.036l-1.666-1.666A6 6 0 017.94 9.827zm9.121 2.292a9.013 9.013 0 00-4.088-5.148l-1.554-1.554a9.008 9.008 0 00-2.805 4.217 6.002 6.002 0 018.447 2.485z" clip-rule="evenodd" />
                `;
            }
        });
    }

    // Aplicar funcionalidad a cada campo de contraseña
    const toggleButtons = document.querySelectorAll('.password-wrapper .toggle-button');

    toggleButtons.forEach((toggleButton, index) => {
        const inputId = ['contrasena', 'contrasena_nueva', 'contrasena_nueva_confirmar'][index];
        togglePasswordVisibility(inputId, toggleButton);
    });
});



