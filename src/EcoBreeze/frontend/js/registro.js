document.addEventListener("DOMContentLoaded", function() {
    // Obtener los mensajes de éxito y error del DOM
    const successMessage = document.getElementById('successMessage').innerText;
    const errorMessage = document.getElementById('errorMessage').innerText;

    // Si hay un mensaje de éxito, mostrar el modal de éxito
    if (successMessage.trim() !== '') {
        // Mostrar el mensaje de éxito en el modal
        $('#successModal .modal-body').text(successMessage);
        $('#successModal').modal('show');
    }
    
    // Si hay un mensaje de error, mostrar el modal de error
    if (errorMessage.trim() !== '') {
        // Mostrar el mensaje de error en el modal
        $('#errorModal .modal-body').text(errorMessage);
        $('#errorModal').modal('show');
    }
});
