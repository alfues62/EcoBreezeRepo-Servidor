<?php
// Obtener el email y el token desde la URL
$email = isset($_GET['email']) ? $_GET['email'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validar que tanto el email como el token han sido proporcionados
if (empty($email) || empty($token)) {
    echo json_encode(['success' => false, 'error' => 'Error al cargar la url, solicite uno nuevo']);
    exit();
}

// Aquí se puede agregar la lógica para procesar el email y el token
// Por ejemplo, verificar si el token es válido o si el email existe en la base de datos.

echo json_encode(['success' => true, 'email' => $email, 'token' => $token]);

// Aquí puedes continuar con la lógica para validar el token, mostrar una vista, etc.
?>
