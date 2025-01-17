document.addEventListener("DOMContentLoaded", function() {
    // Obtener los mensajes de éxito y error del DOM
    const successMessageElement = document.querySelector('.message.success p');
    const errorMessageElement = document.querySelector('.message.error p');

    const successMessage = successMessageElement ? successMessageElement.innerText : '';
    const errorMessage = errorMessageElement ? errorMessageElement.innerText : '';

    // Función para redirigir después de 3 segundos
    function redirectTo(url) {
        setTimeout(function() {
            window.location.href = url;
        }, 3000); // Redirige después de 3 segundos (3000 ms)
    }

    // Si hay un mensaje de éxito, mostrarlo y añadir el aviso de redirección
    if (successMessage.trim() !== '') {
        const finalSuccessMessage = successMessage + "<br>Será redirigido a la página de login.";

        // Aseguramos que el mensaje de éxito se inserte como HTML
        successMessageElement.innerHTML = finalSuccessMessage;

        // Mostrar el mensaje de éxito en consola para depuración
        console.log("Success: ", finalSuccessMessage);
        
        // Redirigir a la página de login después de 3 segundos
        redirectTo('/backend/login/main_login.php');
    }

    // Si hay un mensaje de error, mostrarlo y añadir el aviso de redirección
    if (errorMessage.trim() !== '') {
        const finalErrorMessage = errorMessage + "<br>Será redirigido a la página de inicio.";

        // Aseguramos que el mensaje de error se inserte como HTML
        errorMessageElement.innerHTML = finalErrorMessage;

        // Mostrar el mensaje de error en consola para depuración
        console.log("Error: ", finalErrorMessage);
        
        // Redirigir a la página de inicio después de 3 segundos
        redirectTo('/frontend/index.php');
    }
});
