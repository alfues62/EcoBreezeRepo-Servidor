$(document).ready(function() {
    // Función para ordenar la tabla por columna
    function ordenarTabla(columnaIndex, tipo) {
        const tabla = document.querySelector('table tbody');
        const filas = Array.from(tabla.rows);

        // Función para comparar dos filas según el índice de columna
        const compararFilas = (filaA, filaB) => {
            const celdaA = filaA.cells[columnaIndex].innerText.trim();
            const celdaB = filaB.cells[columnaIndex].innerText.trim();

            if (columnaIndex === 4) { // Columna "Última Medición"
                // Siempre poner "N/A" al final
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
        }

        const filasOrdenadas = filas.sort(compararFilas);

        tabla.innerHTML = '';
        filasOrdenadas.forEach(fila => tabla.appendChild(fila));

        actualizarPaginacion();
    }

    const encabezados = document.querySelectorAll('table th');
    
    encabezados.forEach((encabezado, index) => {
        let tipoOrden = 'asc';

        encabezado.addEventListener('click', () => {
            ordenarTabla(index, tipoOrden);
            tipoOrden = tipoOrden === 'asc' ? 'desc' : 'asc';
        });
    });

    const rowsPerPage = 5;
    let rows = $(".usuarioRow");
    let totalRows = rows.length;
    let totalPages = Math.ceil(totalRows / rowsPerPage);
    let currentPage = 1;

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
            pageItem.on('click', function(e) {
                e.preventDefault();
                showPage(i);
            });
            pagination.append(pageItem);
        }
    }

    function actualizarPaginacion() {
        rows = $(".usuarioRow");
        totalRows = rows.length;
        totalPages = Math.ceil(totalRows / rowsPerPage);
        currentPage = 1;
        showPage(currentPage);
    }

    showPage(currentPage);
    ordenarTabla(4, 'asc');
});
