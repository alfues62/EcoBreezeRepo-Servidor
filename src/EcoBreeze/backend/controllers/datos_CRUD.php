<?php

require_once(__DIR__ . '/../../db/conexion.php');



class DatosCRUD {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Conexion())->getConnection();
    }

    public function leer() {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM MEDICION");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error al leer datos: ' . $e->getMessage(), 3, '../logs/app.log');
            return ['error' => 'Error al leer datos'];
        } 
    }

    public function insertar($data) {
        try {
            $stmt = $this->conexion->prepare("INSERT INTO MEDICION (Valor, Lon, Lat, Fecha, Hora, TIPOGAS_TipoID, `SENSOR_ID Sensor`, UMBRAL_ID) VALUES (:valor, :lon, :lat, :fecha, :hora, :tipogas, :sensor, :umbral)");
            $stmt->bindParam(':valor', $data['Valor']);
            $stmt->bindParam(':lon', $data['Lon']);
            $stmt->bindParam(':lat', $data['Lat']);
            $stmt->bindParam(':fecha', $data['Fecha']);
            $stmt->bindParam(':hora', $data['Hora']);
            $stmt->bindParam(':tipogas', $data['TIPOGAS_TipoID']);
            $stmt->bindParam(':sensor', $data['SENSOR_ID Sensor']);
            $stmt->bindParam(':umbral', $data['UMBRAL_ID']);
            $stmt->execute();
            return ['mensaje' => 'Registro insertado exitosamente'];
        } catch (Exception $e) {
            error_log('Error al insertar datos: ' . $e->getMessage(), 3, '../logs/app.log');
            return ['error' => 'Error al insertar datos'];
        }
    }

    public function editar($data) {
        try {
            $stmt = $this->conexion->prepare("UPDATE MEDICION SET Valor = :valor, Lon = :lon, Lat = :lat, Fecha = :fecha, Hora = :hora, TIPOGAS_TipoID = :tipogas, `SENSOR_ID Sensor` = :sensor, UMBRAL_ID = :umbral WHERE IDMedicion = :id");
            $stmt->bindParam(':valor', $data['Valor']);
            $stmt->bindParam(':lon', $data['Lon']);
            $stmt->bindParam(':lat', $data['Lat']);
            $stmt->bindParam(':fecha', $data['Fecha']);
            $stmt->bindParam(':hora', $data['Hora']);
            $stmt->bindParam(':tipogas', $data['TIPOGAS_TipoID']);
            $stmt->bindParam(':sensor', $data['SENSOR_ID Sensor']);
            $stmt->bindParam(':umbral', $data['UMBRAL_ID']);
            $stmt->bindParam(':id', $data['IDMedicion']);
            $stmt->execute();
            return ['mensaje' => 'Registro actualizado exitosamente'];
        } catch (Exception $e) {
            error_log('Error al editar datos: ' . $e->getMessage(), 3, '../logs/app.log');
            return ['error' => 'Error al editar datos'];
        }
    }

    public function borrar($id) {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM MEDICION WHERE IDMedicion = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return ['mensaje' => 'Registro borrado exitosamente'];
        } catch (Exception $e) {
            error_log('Error al borrar datos: ' . $e->getMessage(), 3, '../logs/app.log');
            return ['error' => 'Error al borrar datos'];
        }
    }
}
