<?php
header('Content-Type: application/json');
include '../db/conexion.php'; // Asegúrate de incluir la conexión

// Obtener el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Validar los datos recibidos
if (isset($data['valor_major']) && isset($data['valor_minor'])) {
    $valor_major = $data['valor_major'];
    $valor_minor = $data['valor_minor'];

    // Escapar los valores para evitar inyecciones SQL
    $valor_major = mysqli_real_escape_string($conn, $valor_major);
    $valor_minor = mysqli_real_escape_string($conn, $valor_minor);

    // Inserción en la base de datos
    $query = "INSERT INTO acciones (valor_major, valor_minor) VALUES ('$valor_major', '$valor_minor')";
    $result = mysqli_query($conn, $query); // Cambiar mysql_query a mysqli_query

    if ($result) {
        echo json_encode(["message" => "Medición insertada correctamente"]);
    } else {
        http_response_code(500); // Error del servidor
        echo json_encode(["error" => "Error al insertar la medición: " . mysqli_error($conn)]); // Corregir mysq_error a mysqli_error
    }
} else {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["error" => "Datos faltantes"]);
}
?>
