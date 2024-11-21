document.addEventListener("DOMContentLoaded", function () {
    // Verificar si hay un mensaje de error en el HTML
    const errorMessage = document.querySelector('.message.error');

    // Si hay un mensaje de error, mostrar el aviso y redirigir después de 3 segundos
    if (errorMessage) {
        // Mostrar mensaje de redirección
        const redirectionMessage = document.getElementById('redirection-message');
        redirectionMessage.style.display = 'block'; // Hacer visible el mensaje

        // Redirigir después de 3 segundos
        setTimeout(function () {
            window.location.href = "/frontend/index.php"; // Cambiar la ruta si es necesario
        }, 3000);
    }

    // Obtener los elementos del formulario
    const passwordField = document.getElementById("nuevaContrasena");
    const confirmPasswordField = document.getElementById("confirmarContrasena");
    const errorMessageElement = document.getElementById("password-error");
    const submitButton = document.getElementById("submit-btn");

    // Función para verificar si las contraseñas coinciden
    function validatePasswords() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        let error = "";

        // Comprobar si las contraseñas coinciden
        if (password !== confirmPassword) {
            error = "Las contraseñas no coinciden.";
        } 
        // Si deseas activar la validación de complejidad más adelante, descomenta la siguiente línea
        // else if (!checkPasswordComplexity(password)) {
        //     error = "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, un número y un carácter especial.";
        // }

        // Mostrar el error si existe
        if (error) {
            errorMessageElement.textContent = error;
            errorMessageElement.style.display = "block"; // Hacer visible el error
            submitButton.disabled = true; // Desactivar el botón de envío
        } else {
            errorMessageElement.style.display = "none"; // Ocultar el error
            submitButton.disabled = false; // Activar el botón de envío
        }
    }

    // Escuchar cambios en los campos de contraseña
    passwordField.addEventListener("input", validatePasswords);
    confirmPasswordField.addEventListener("input", validatePasswords);
});
