<?php
require_once '/var/www/html/db/conexion.php';

class MedicionesController {
    
    public function leer() {
        global $conn;
        $sql = "SELECT * FROM acciones";
        $result = $conn->query($sql);
        
        $mediciones = [];
        while ($row = $result->fetch_assoc()) {
            $mediciones[] = $row;
        }
        
        echo json_encode($mediciones);
    }

    public function leerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM acciones WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medicion = $result->fetch_assoc();
        
        echo json_encode($medicion);
    }

    public function insertar($data) {
        global $conn;
        $sql = "INSERT INTO acciones (numero) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $data['valor_major']);
        $stmt->execute();
        
        echo json_encode(["status" => "success"]);
    }

    public function editar($data) {
        global $conn;
        $sql = "UPDATE acciones SET numero = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $data['valor_major'], $data['id_medicion']);
        $stmt->execute();
        
        echo json_encode(["status" => "success"]);
    }

    public function borrar($id) {
        global $conn;
        $sql = "DELETE FROM acciones WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        echo json_encode(["status" => "success"]);
    }
}
?>
