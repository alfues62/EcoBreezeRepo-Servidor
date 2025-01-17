document.addEventListener("DOMContentLoaded", function() {
    const masInfoButton = document.querySelector(".masInfo");
    if (masInfoButton) {
        masInfoButton.addEventListener('click', function() {
            // Seleccionar todos los bloques de contaminantes
            const contaminantBlocks = document.querySelectorAll('.contaminant');
            const moreInfoElements = document.querySelectorAll('.more-info');
            
            // Alternar la visibilidad de la información y la expansión de los bloques
            contaminantBlocks.forEach(block => {
                const moreInfo = block.querySelector('.more-info');
                block.classList.toggle('expanded'); // Expande o colapsa el bloque
                moreInfo.classList.toggle('visible'); // Muestra o oculta la información adicional
            });

            // Cambiar el texto del botón "Más información" a "Menos Info"
            if (masInfoButton.textContent === "Más información") {
                masInfoButton.textContent = "Menos información"; // Cambiar el texto a "Menos Info"
            } else {
                masInfoButton.textContent = "Más información"; // Cambiar el texto a "Más información"
            }
        });
    }
});
