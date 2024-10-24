<?php
require_once '../db/conexion.php'; // Asegúrate de que la ruta sea correcta
require_once '../controllers/datos_CRUD.php';

// Capturar la operación desde la solicitud
$operacion = $_GET['operacion'] ?? null;

$datosCRUD = new DatosCRUD();

header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON

try {
    switch ($operacion) {
        case 'leer':
            $result = $datosCRUD->leer();
            if (isset($result['error'])) {
                // Si hay un error en la operación de lectura
                http_response_code(500); // Error interno del servidor
                echo json_encode([
                    'error' => 'Error al leer los datos: ' . $result['error']
                ]);
            } else {
                http_response_code(200); // OK
                echo json_encode($result);
            }
            break;

        case 'insertar':
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data)) {
                // Si no se envían datos válidos
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Datos de entrada no válidos. Asegúrate de enviar un objeto JSON con la información correcta.']);
                break;
            }
            $result = $datosCRUD->insertar($data);
            if (isset($result['error'])) {
                // Si hay un error en la operación de inserción
                http_response_code(500); // Error interno del servidor
                echo json_encode([
                    'error' => 'Error al insertar datos: ' . $result['error']
                ]);
            } else {
                http_response_code(201); // Created
                echo json_encode($result);
            }
            break;

        case 'editar':
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data) || !isset($data['IDMedicion'])) {
                // Si no se envían datos válidos o falta el ID de medición
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Datos de entrada no válidos. Asegúrate de incluir el IDMedicion en el objeto JSON.']);
                break;
            }
            $result = $datosCRUD->editar($data);
            if (isset($result['error'])) {
                // Si hay un error en la operación de edición
                http_response_code(500); // Error interno del servidor
                echo json_encode([
                    'error' => 'Error al editar datos: ' . $result['error']
                ]);
            } else {
                http_response_code(200); // OK
                echo json_encode($result);
            }
            break;

        case 'borrar':
            $id = $_GET['id'] ?? null;
            if (is_null($id)) {
                // Si no se proporciona un ID para borrar
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'ID de medición no proporcionado. Asegúrate de enviar un ID válido en la solicitud.']);
                break;
            }
            $result = $datosCRUD->borrar($id);
            if (isset($result['error'])) {
                // Si hay un error en la operación de borrado
                http_response_code(500); // Error interno del servidor
                echo json_encode([
                    'error' => 'Error al borrar datos: ' . $result['error']
                ]);
            } else {
                http_response_code(200); // OK
                echo json_encode($result);
            }
            break;

        default:
            // Si la operación solicitada no es válida
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Operación no válida. Por favor, verifica la operación solicitada y vuelve a intentarlo.']);
            break;
    }
} catch (Exception $e) {
    // Captura cualquier excepción no controlada y proporciona información útil
    http_response_code(500); // Error interno del servidor
    echo json_encode([
        'error' => 'Se produjo un error inesperado: ' . $e->getMessage() . '. Por favor, contacta al soporte técnico.'
    ]);
}
