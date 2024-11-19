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

}
