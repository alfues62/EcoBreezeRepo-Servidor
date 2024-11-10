document.addEventListener("DOMContentLoaded", function() {
    // Obtener los mensajes de éxito y error del DOM
    const successMessageElement = document.getElementById('successMessage');
    const errorMessageElement = document.getElementById('errorMessage');

    const successMessage = successMessageElement ? successMessageElement.innerText : '';
    const errorMessage = errorMessageElement ? errorMessageElement.innerText : '';

    // Función para redirigir después de 5 segundos
    function redirectTo(url) {
        setTimeout(function() {
            window.location.href = url;
        }, 5000); // Redirige después de 5 segundos (5000 ms)
    }

    // Si hay un mensaje de éxito, mostrar el modal de éxito y redirigir a login
    if (successMessage.trim() !== '') {
        // Añadir el aviso de redirección al mensaje de éxito
        const finalSuccessMessage = successMessage + "\nSerá redirigido a la página de login.";
        
        // Mostrar el mensaje de éxito en el modal
        document.getElementById('successModalMessage').innerText = finalSuccessMessage;
        document.getElementById('successModal').style.display = 'block';
        
        // Redirigir a la página de login después de 5 segundos
        redirectTo('/backend/login/main_login.php');
    }
    
    // Si hay un mensaje de error, mostrar el modal de error y redirigir a index
    if (errorMessage.trim() !== '') {
        // Añadir el aviso de redirección al mensaje de error
        const finalErrorMessage = errorMessage + "\nSerá redirigido a la página de inicio.";
        
        // Mostrar el mensaje de error en el modal
        document.getElementById('errorModalMessage').innerText = finalErrorMessage;
        document.getElementById('errorModal').style.display = 'block';
        
        // Redirigir a la página de inicio después de 5 segundos
        redirectTo('/frontend/index.php');
    }
});
