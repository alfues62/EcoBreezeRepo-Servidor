<?php
include_once '../../db/conexion.php';
include_once '../../controllers/AccionController.php';

header('Content-Type: application/json');

// Obtener la URI para determinar la acción
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Conectar a la base de datos
$conn = (new Conexion())->getConnection();

// Crear instancia del controlador
$controller = new AccionController($conn);

switch ($method) {
    case 'GET':
        if (preg_match('/\/leer\/(\d+)/', $requestUri, $matches)) {
            $id = intval($matches[1]);
            try {
                $result = $controller->leerAccionPorId($id);
                echo json_encode($result);
            } catch (Exception $e) {
                error_log($e->getMessage(), 3, '../../logs/app.log');
                http_response_code(500);
                echo json_encode(['error' => 'Error del servidor']);
            }
        } else {
            try {
                $result = $controller->leerTodasLasAcciones();
                echo json_encode($result);
            } catch (Exception $e) {
                error_log($e->getMessage(), 3, '../../logs/app.log');
                http_response_code(500);
                echo json_encode(['error' => 'Error del servidor']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['numero'])) {
            try {
                $controller->insertarAccion($data['numero']);
                echo json_encode(['message' => 'Acción insertada']);
            } catch (Exception $e) {
                error_log($e->getMessage(), 3, '../../logs/app.log');
                http_response_code(500);
                echo json_encode(['error' => 'Error del servidor']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id']) && isset($data['numero'])) {
            try {
                $controller->editarAccion($data['id'], $data['numero']);
                echo json_encode(['message' => 'Acción editada']);
            } catch (Exception $e) {
                error_log($e->getMessage(), 3, '../../logs/app.log');
                http_response_code(500);
                echo json_encode(['error' => 'Error del servidor']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
        }
        break;

    case 'DELETE':
        if (preg_match('/\/borrar\/(\d+)/', $requestUri, $matches)) {
            $id = intval($matches[1]);
            try {
                $controller->borrarAccion($id);
                echo json_encode(['message' => 'Acción eliminada']);
            } catch (Exception $e) {
                error_log($e->getMessage(), 3, '../../logs/app.log');
                http_response_code(500);
                echo json_encode(['error' => 'Error del servidor']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
