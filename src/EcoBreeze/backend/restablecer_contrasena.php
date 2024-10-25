<?php
// restablecer_contrasena.php
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verifica que el token sea válido y que no haya expirado
    // Lógica de validación de token en la base de datos
?>

<form action="actualizar_contrasena.php" method="POST">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <label for="nueva_contrasena">Nueva Contraseña:</label>
    <input type="password" name="nueva_contrasena" required>
    <label for="confirmar_contrasena">Confirmar Contraseña:</label>
    <input type="password" name="confirmar_contrasena" required>
    <button type="submit">Restablecer contraseña</button>
</form>

<?php } else {
    echo "Token inválido o expirado.";
} ?>
