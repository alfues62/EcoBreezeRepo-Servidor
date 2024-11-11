<?php
session_start();
session_destroy(); // Destruye todas las variables de sesión
header("Location: login/main_login.php"); // Redirige a la página de login
exit;
?>
