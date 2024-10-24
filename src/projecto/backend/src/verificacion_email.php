<?php 
require_once '../controllers/usuario_CRUD.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $usuariosCRUD = new UsuariosCRUD();
    
    // Llama al método en UsuariosCRUD para verificar el token
    $resultado = $usuariosCRUD->verificarEmail($token);
    
    if ($resultado) {
        echo "Tu correo ha sido verificado con éxito.";
    } else {
        echo "Error al verificar el correo: El token es inválido o ya ha sido utilizado.";
    }
} else {
    echo "Token no proporcionado.";
}
?>
