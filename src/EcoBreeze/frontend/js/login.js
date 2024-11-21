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


    // Iconos SVG para el ojo abierto y cerrado
    const eyeIcons = {
        open: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="eye-icon">
                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd"/>
            </svg>`,
        closed: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="eye-icon">
                    <path d="M3.53 2.47a.75.75 0 00-1.06 1.06l18 18a.75.75 0 101.06-1.06l-18-18zM22.676 12.553a11.249 11.249 0 01-2.631 4.31l-3.099-3.099a5.25 5.25 0 00-6.71-6.71L7.759 4.577a11.217 11.217 0 014.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113z" />
                    <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0115.75 12zM12.53 15.713l-4.243-4.244a3.75 3.75 0 004.243 4.243z" />
                    <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 00-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 016.75 12z" />
                </svg>`
    };

    // Función para añadir listeners
    function addListeners() {
        const toggleButton = document.querySelector(".toggle-button");
        if (!toggleButton) return;

        toggleButton.addEventListener("click", togglePassword);
    }

    // Función para alternar visibilidad de contraseña
    function togglePassword() {
        const passwordField = document.querySelector("#contrasena");
        const toggleButton = document.querySelector(".toggle-button");

        if (!passwordField || !toggleButton) return;

        const isEyeOpen = toggleButton.classList.toggle("open");
        toggleButton.innerHTML = isEyeOpen ? eyeIcons.closed : eyeIcons.open;
        passwordField.type = isEyeOpen ? "text" : "password";
    }

    // Iniciar script al cargar
    document.addEventListener("DOMContentLoaded", addListeners);

});