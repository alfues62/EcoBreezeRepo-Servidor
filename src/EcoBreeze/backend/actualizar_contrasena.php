<?php
// Recoge la nueva contraseña y el token
$nueva_contrasena = $_POST['nueva_contrasena'];
$confirmar_contrasena = $_POST['confirmar_contrasena'];
$token = $_POST['token'];

if ($nueva_contrasena === $confirmar_contrasena) {
    // Hashea la nueva contraseña
    $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
    
    // Actualiza la contraseña en la base de datos y elimina el token
    // Lógica para actualizar la contraseña y eliminar el token

    echo "Tu contraseña ha sido restablecida exitosamente.";
} else {
    echo "Las contraseñas no coinciden.";
}
?>
