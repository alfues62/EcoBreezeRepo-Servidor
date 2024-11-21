<?php

require_once(__DIR__ . '/../../db/conexion.php');



class DatosCRUD {
    private $conn;
    private $logFile; 

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
        $this->logFile = '/var/www/html/logs/app.log'; // Establecer la ruta del archivo de log
    }

    public function obtenerMedicionesUsuario($usuarioId) {
        try {
            $query = "
                SELECT 
                    m.IDMedicion, 
                    m.Valor, 
                    m.Lon, 
                    m.Lat, 
                    m.Fecha, 
                    m.Hora, 
                    m.Categoria,
                    m.TIPOGAS_TipoID,
                    tg.TipoGas
                FROM 
                    MEDICION m
                INNER JOIN 
                    SENSOR s ON m.SENSOR_ID_Sensor = s.SensorID
                INNER JOIN 
                    USUARIO u ON s.USUARIO_ID = u.ID
                INNER JOIN 
                    TIPOGAS tg ON m.TIPOGAS_TipoID = tg.TipoID
                WHERE 
                    u.ID = ?
                ORDER BY 
                    m.Fecha DESC, m.Hora DESC;
            ";
    
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuarioId]);
    
            if ($stmt->rowCount() > 0) {
                $mediciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => true, 'mediciones' => $mediciones]);
            } else {
                return json_encode(['success' => false, 'error' => 'No se encontraron mediciones para el usuario.']);
            }
        } catch (PDOException $e) {
            error_log("Error en obtener mediciones usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
            return json_encode(['success' => false, 'error' => 'Error al obtener las mediciones del usuario.']);
        }
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
