<?php
/**
 * Cierra la sesión del usuario y redirige a la página de login.
 *
 * Este script destruye todas las variables de sesión activas y redirige al usuario a la página de inicio de sesión.
 * Puedes modificar la ruta de redirección en caso de que necesites cambiar el destino después de cerrar la sesión.
 * 
 * Diseño:
 *
 * Proceso:
 *   1. Inicia la sesión con `session_start()`, lo que permite manipular las variables de sesión.
 *   2. Llama a `session_destroy()` para destruir todas las variables de sesión y cerrar la sesión del usuario.
 *   3. Redirige al usuario a la página de login especificada en `header("Location: ...")`.
 *   4. La función `exit` asegura que el script termine y la redirección sea ejecutada.
 *
 * Salida:
 *   - El usuario será redirigido a la página de login.
 *
 * @return void
 */

// Inicia la sesión
session_start();

// Destruye todas las variables de sesión
session_destroy(); // Si deseas destruir una sesión específica, puedes usar session_unset() en lugar de session_destroy()

header("Location: login/main_login.php"); // Asegúrate de que la ruta sea correcta para tu proyecto

exit; // Es recomendable usar exit después de redirigir para evitar que el script siga ejecutándose
?>
