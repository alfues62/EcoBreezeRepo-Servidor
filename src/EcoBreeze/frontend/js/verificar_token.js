document.addEventListener("DOMContentLoaded", function() {
    // Obtener los mensajes de éxito y error del DOM
    const successMessageElement = document.querySelector('.message.success p');
    const errorMessageElement = document.querySelector('.message.error p');

    const successMessage = successMessageElement ? successMessageElement.innerText : '';
    const errorMessage = errorMessageElement ? errorMessageElement.innerText : '';

    // Función para redirigir después de 5 segundos
    function redirectTo(url) {
        setTimeout(function() {
            window.location.href = url;
        }, 3000); // Redirige después de 5 segundos (5000 ms)
    }

    // Si hay un mensaje de éxito, mostrarlo y añadir el aviso de redirección
    if (successMessage.trim() !== '') {
        const finalSuccessMessage = successMessage + "<br>Será redirigido a la página de login.";
        successMessageElement.innerHTML = finalSuccessMessage; // Actualizar el mensaje en el DOM

        // Mostrar el mensaje de éxito
        console.log("Success: ", finalSuccessMessage);  // Puedes reemplazar esto con la lógica para mostrar el éxito en la interfaz
        
        // Redirigir a la página de login después de 5 segundos
        redirectTo('/backend/login/main_login.php');
    }

    // Si hay un mensaje de error, mostrarlo y añadir el aviso de redirección
    if (errorMessage.trim() !== '') {
        const finalErrorMessage = errorMessage + "<br>Será redirigido a la página de inicio.";
        errorMessageElement.innerHTML = finalErrorMessage; // Actualizar el mensaje en el DOM

        // Mostrar el mensaje de error
        console.log("Error: ", finalErrorMessage);  // Puedes reemplazar esto con la lógica para mostrar el error en la interfaz
        
        // Redirigir a la página de inicio después de 5 segundos
        redirectTo('/frontend/index.php');
    }
});
