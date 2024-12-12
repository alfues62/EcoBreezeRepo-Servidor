$(document).ready(function () {
    const rowsPerPage = 5;
    let rows = $(".usuarioRow");
    let totalRows = rows.length;
    let totalPages = Math.ceil(totalRows / rowsPerPage);
    let currentPage = 1;
    let currentSortColumn = 4;  // Por defecto, ordenamos por "Última Medición"
    let currentSortOrder = 'asc';

    function ordenarTabla(columnaIndex, tipo) {
        currentSortColumn = columnaIndex;
        currentSortOrder = tipo;

        const tabla = document.querySelector('table tbody');
        const filas = Array.from(tabla.rows);

        filas.sort((filaA, filaB) => {
            const celdaA = filaA.cells[columnaIndex].innerText.trim();
            const celdaB = filaB.cells[columnaIndex].innerText.trim();

            if (columnaIndex === 4) {
                if (celdaA === 'N/A' && celdaB !== 'N/A') return 1;
                if (celdaB === 'N/A' && celdaA !== 'N/A') return -1;

                const fechaA = new Date(celdaA);
                const fechaB = new Date(celdaB);
                return tipo === 'asc' ? fechaA - fechaB : fechaB - fechaA;
            }

            if (!isNaN(celdaA) && !isNaN(celdaB)) {
                return tipo === 'asc' ? parseFloat(celdaA) - parseFloat(celdaB) : parseFloat(celdaB) - parseFloat(celdaA);
            }

            return tipo === 'asc' ? celdaA.localeCompare(celdaB) : celdaB.localeCompare(celdaA);
        });

        tabla.innerHTML = '';
        filas.forEach(fila => tabla.appendChild(fila));

        actualizarPaginacion();
        mostrarPagina(currentPage);
    }

    $('table').on('click', '.bi-caret-up-fill', function () {
        ordenarTabla($(this).closest('th').index(), 'asc');
    });

    $('table').on('click', '.bi-caret-down-fill', function () {
        ordenarTabla($(this).closest('th').index(), 'desc');
    });

    function guardarEstadoOriginal() {
        $("table tbody tr").each(function () {
            $(this).find("td").eq(4).attr("data-ultima-medicion", $(this).find("td").eq(4).html());
        });
    }

    function mostrarBotonesEliminar() {
        $('th').eq(4).text('Acción');
        $("table tbody tr").each(function () {
            const celda = $(this).find("td").eq(4);
            if (celda.html().trim() !== '&nbsp;') {
                celda.html('<button class="eliminarUsuarioBtn">Eliminar Usuario</button>');
            }
        });
        ordenarTabla(currentSortColumn, currentSortOrder);
    }

    function mostrarUltimaMedicion() {
        $('th').eq(4).html(`
            Última Medición
            <i class="bi bi-caret-up-fill cursor-pointer"></i>
            <i class="bi bi-caret-down-fill cursor-pointer"></i>
        `);
        $("table tbody tr").each(function () {
            const celda = $(this).find("td").eq(4);
            celda.html(celda.attr("data-ultima-medicion"));
        });
        ordenarTabla(currentSortColumn, currentSortOrder);
    }

    function mostrarPagina(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.hide().slice(start, end).show();
        $(".emptyRow").remove();

        const rowsDisplayed = end > totalRows ? totalRows - start : rowsPerPage;
        const remainingRows = rowsPerPage - rowsDisplayed;

        for (let i = 0; i < remainingRows; i++) {
            const emptyRow = $("<tr>").addClass("emptyRow");
            for (let j = 0; j < rows.first().children().length; j++) {
                emptyRow.append("<td>&nbsp;</td>");
            }
            $("table tbody").append(emptyRow);
        }

        actualizarPaginacion(page);
    }

    function actualizarPaginacion(page) {
        const pagination = $("#pagination").empty();

        for (let i = 1; i <= totalPages; i++) {
            const pageItem = $('<li class="page-item"><a class="page-link" href="#">' + i + '</a></li>');
            if (i === page) {
                pageItem.addClass('active');
            }
            pageItem.on('click', function (e) {
                e.preventDefault();
                currentPage = i;
                mostrarPagina(i);
            });
            pagination.append(pageItem);
        }
    }

    $(document).on('click', '#eliminarUsuarioBtn', function () {
        mostrarBotonesEliminar();
    });

    $(document).on('click', '#ultimaMedicionBtn', function () {
        mostrarUltimaMedicion();
    });

    $(document).on('click', '.eliminarUsuarioBtn', function () {
        const fila = $(this).closest('tr');
        const usuarioId = fila.find('td').eq(0).text();

        const usuarioDatos = `
            <strong>ID:</strong> ${usuarioId}<br>
            <strong>Nombre:</strong> ${fila.find('td').eq(1).text()}<br>
            <strong>Apellidos:</strong> ${fila.find('td').eq(2).text()}<br>
            <strong>Email:</strong> ${fila.find('td').eq(3).text()}
        `;
        $('#modalUsuarioDatos').html(`
            ¿Estás seguro de que deseas eliminar al siguiente usuario?<br>
            ${usuarioDatos}
        `);
        $('#confirmarEliminarModal').modal('show');

        $('#confirmarEliminarBtn').off('click').on('click', function () {
            $('#deleteUserId').val(usuarioId);
            $('#deleteForm').submit();
        });
    });

    const successMessage = $('#successMessage').html();
    const errorMessage = $('#errorMessage').html();
    if (successMessage || errorMessage) {
        $('#resultadoModalBody').html(successMessage ? successMessage : errorMessage);
        $('#resultadoModal').modal('show');
    }

    guardarEstadoOriginal();
    $('#ultimaMedicionBtn').trigger('click');
    mostrarPagina(currentPage);
    ordenarTabla(4, 'asc');
});
