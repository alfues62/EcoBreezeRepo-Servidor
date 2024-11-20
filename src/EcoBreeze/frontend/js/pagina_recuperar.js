document.addEventListener("DOMContentLoaded", function() {
    // Comprobar si existe un error PHP
        if (errorPHP) {
        // Si hay un error, redirigir al login después de 3 segundos
        setTimeout(function() {
            window.location.href = "/login.php"; // Cambiar la ruta si es necesario
        }, 3000); // 3000 milisegundos = 3 segundos
    }

    // Validación del formulario al enviarlo
    document.getElementById('cambiarContrasenaRecuperar')?.addEventListener('submit', function(event) {
        var nuevaContraseña = document.getElementById('nueva_contraseña').value;
        var confirmarContraseña = document.getElementById('confirmar_contraseña').value;

        // Validación de contraseñas coincidentes
        if (nuevaContraseña !== confirmarContraseña) {
            event.preventDefault();
            alert('Las contraseñas no coinciden. Por favor, intente nuevamente.');
        }

        // Validación de la complejidad de la nueva contraseña
        var contrasenaCompleja = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!contrasenaCompleja.test(nuevaContraseña)) {
            event.preventDefault();
            alert('La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.');
        }
    });
});
