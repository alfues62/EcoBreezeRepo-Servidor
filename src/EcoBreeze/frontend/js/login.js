document.addEventListener("DOMContentLoaded", function() {
    console.log('login.js loaded'); // Comprobación para ver si el script se carga

    // Selecciona el formulario de inicio de sesión
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const contrasenaInput = document.getElementById('contrasena');
    const errorModal = $('#errorModal'); // Modal de error
    const errorMessageInput = document.getElementById('errorMessage');
    const errorMessageContent = document.getElementById('errorMessageContent');

    console.log('Elements selected'); // Comprobación para ver si los elementos se seleccionan

    // Añade un evento al formulario para la validación antes de enviarlo
    form.addEventListener('submit', function(event) {
        console.log('Form submit event triggered'); // Comprobación para ver si se detona el evento submit

        // Resetea el mensaje de error antes de la validación
        let errorMessage = '';

        // Valida el correo electrónico
        const email = emailInput.value.trim();
        if (!validateEmail(email)) {
            errorMessage = 'Por favor, ingresa un correo electrónico válido.';
        }

        // Valida la contraseña (solo que no esté vacía)
        const contrasena = contrasenaInput.value.trim();
        if (!contrasena) {  // Si no hay contraseña
            errorMessage = 'La contraseña no puede estar vacía.';
        }

        // Si hay errores, muestra el modal y previene el envío del formulario
        if (errorMessage) {
            event.preventDefault(); // Prevenir el envío del formulario
            console.log('Showing client-side error:', errorMessage); // Comprobación para ver si se maneja el error
            showError(errorMessage);
        }
    });

    // Muestra el modal si hay un mensaje de error en el campo hidden
    const serverErrorMessage = errorMessageInput.value.trim();
    console.log('Server Error Message:', serverErrorMessage); // Comprobación para ver si el mensaje del servidor se lee correctamente
    if (serverErrorMessage) {
        showError(serverErrorMessage);
    }

    // Función para validar el correo electrónico con una expresión regular simple
    function validateEmail(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(email);
    }

    // Función para mostrar el mensaje de error en el modal
    function showError(message) {
        console.log('Showing Error:', message); // Comprobación para ver si se muestra el error
        // Añadir el mensaje al modal
        errorMessageContent.innerHTML = `<p>${message}</p>`;

        // Mostrar el modal de error
        errorModal.modal('show');
    }
});
