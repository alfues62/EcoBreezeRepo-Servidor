document.addEventListener('DOMContentLoaded', function() {
    // Si no hay error, redirigir después de unos segundos
    if (!document.querySelector('.message.error')) {
        setTimeout(function() {
            window.location.href = 'login.php'; // Redirige al login
        }, 3000); // Redirige después de 3 segundos
    }
});
