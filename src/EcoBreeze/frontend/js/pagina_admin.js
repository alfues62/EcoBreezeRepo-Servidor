$(document).ready(function () {
    const rowsPerPage = 5;
    let rows = $(".usuarioRow");
    let totalRows = rows.length;
    let totalPages = Math.ceil(totalRows / rowsPerPage);
    let currentPage = 1;

    // Función para ordenar la tabla por columna
    function ordenarTabla(columnaIndex, tipo) {
        const tabla = document.querySelector('table tbody');
        const filas = Array.from(tabla.rows);

        const compararFilas = (filaA, filaB) => {
            const celdaA = filaA.cells[columnaIndex].innerText.trim();
            const celdaB = filaB.cells[columnaIndex].innerText.trim();

            if (columnaIndex === 4) { // Ordenar "Última Medición"
                if (celdaA === 'N/A' && celdaB !== 'N/A') return 1;
                if (celdaB === 'N/A' && celdaA !== 'N/A') return -1;

                const fechaA = new Date(celdaA);
                const fechaB = new Date(celdaB);

                return tipo === 'asc' ? fechaA - fechaB : fechaB - fechaA;
            }

            if (!isNaN(celdaA) && !isNaN(celdaB)) {
                return tipo === 'asc' 
                    ? parseFloat(celdaA) - parseFloat(celdaB) 
                    : parseFloat(celdaB) - parseFloat(celdaA);
            }

            return tipo === 'asc'
                ? celdaA.localeCompare(celdaB)
                : celdaB.localeCompare(celdaA);
        };

        const filasOrdenadas = filas.sort(compararFilas);

        tabla.innerHTML = '';
        filasOrdenadas.forEach(fila => tabla.appendChild(fila));

        actualizarPaginacion();
        showPage(currentPage);
    }

    // Delegación de eventos para las flechas de ordenación
    $('table').on('click', '.bi-caret-up-fill', function () {
        const columnaIndex = $(this).closest('th').index();
        ordenarTabla(columnaIndex, 'asc');
    });

    $('table').on('click', '.bi-caret-down-fill', function () {
        const columnaIndex = $(this).closest('th').index();
        ordenarTabla(columnaIndex, 'desc');
    });

    // Guardar el estado original de la columna
    function guardarEstadoOriginal() {
        $("table tbody tr").each(function () {
            const celda = $(this).find("td").eq(4);
            celda.attr("data-ultima-medicion", celda.html());
        });
    }

    // Cambiar la columna a botones de eliminar usuario
    function mostrarBotonesEliminar() {
        $('th').eq(4).text('Acción');
        $("table tbody tr").each(function () {
            const celda = $(this).find("td").eq(4);
            celda.html('<button class="eliminarUsuarioBtn">Eliminar Usuario</button>');
        });
    }

    // Restaurar la columna a "Última Medición"
    function mostrarUltimaMedicion() {
        const th = $('th').eq(4);
        th.html(`
            Última Medición
            <i class="bi bi-caret-up-fill cursor-pointer"></i>
            <i class="bi bi-caret-down-fill cursor-pointer"></i>
        `);

        $("table tbody tr").each(function () {
            const celda = $(this).find("td").eq(4);
            const ultimaMedicion = celda.attr("data-ultima-medicion");
            celda.html(ultimaMedicion);
        });

        ordenarTabla(4, 'asc');
    }

    // Mostrar la página actual de la tabla
    function showPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.hide().slice(start, end).show();
        $(".emptyRow").remove();

        const rowsDisplayed = end > totalRows ? totalRows - start : rowsPerPage;
        const remainingRows = rowsPerPage - rowsDisplayed;

        if (remainingRows > 0) {
            for (let i = 0; i < remainingRows; i++) {
                const emptyRow = $("<tr>").addClass("emptyRow");
                const columnCount = rows.first().children().length;
                for (let j = 0; j < columnCount; j++) {
                    emptyRow.append("<td>&nbsp;</td>");
                }
                $("table tbody").append(emptyRow);
            }
        }

        updatePagination(page);
    }

    function updatePagination(page) {
        const pagination = $("#pagination");
        pagination.empty();

        for (let i = 1; i <= totalPages; i++) {
            const pageItem = $('<li class="page-item"><a class="page-link" href="#">' + i + '</a></li>');
            if (i === page) {
                pageItem.addClass('active');
            }
            pageItem.on('click', function (e) {
                e.preventDefault();
                currentPage = i;
                showPage(i);
            });
            pagination.append(pageItem);
        }
    }

    function actualizarPaginacion() {
        rows = $(".usuarioRow");
        totalRows = rows.length;
        totalPages = Math.ceil(totalRows / rowsPerPage);
        showPage(currentPage);
    }

    // Manejo de botones
    $(document).on('click', '#eliminarUsuarioBtn', function () {
        mostrarBotonesEliminar();
    });

    $(document).on('click', '#ultimaMedicionBtn', function () {
        mostrarUltimaMedicion();
    });

    // Inicializar la vista
    guardarEstadoOriginal();
    $('#ultimaMedicionBtn').trigger('click');
    showPage(currentPage);
    ordenarTabla(4, 'asc');
});
