document.getElementById('recuperar-form').addEventListener('submit', function(event) {
    // Validación de contraseñas
    var nuevaContraseña = document.getElementById('nueva_contraseña').value;
    var confirmarContraseña = document.getElementById('confirmar_contraseña').value;

    if (nuevaContraseña !== confirmarContraseña) {
        alert("Las contraseñas no coinciden.");
        event.preventDefault(); // Evitar el envío del formulario
    }

    // Aquí podrías agregar más validaciones si es necesario
});
