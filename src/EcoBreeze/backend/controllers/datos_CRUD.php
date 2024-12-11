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



    public function insertarMedicionesAPI($mediciones) {
        try {
            // Comenzamos la transacción para asegurar la consistencia de los datos
            $this->conn->beginTransaction();
    
            // Consulta SQL para insertar una medición en la tabla MEDICIONESAPI
            $query = "
                INSERT INTO `MEDICIONESAPI` 
                (`Valor`, `Lon`, `Lat`, `Fecha`, `Hora`, `TIPOGAS_TipoID`)
                VALUES (?, ?, ?, ?, ?, ?)
            ";
    
            // Preparamos la consulta
            $stmt = $this->conn->prepare($query);
    
            // Recorremos cada medición y la insertamos en la base de datos
            foreach ($mediciones as $medicion) {
                $valor = $medicion['value'];
                $lon = $medicion['longitude'];
                $lat = $medicion['latitude'];
                $fecha = date('Y-m-d', strtotime($medicion['time']));
                $hora = date('H:i:s', strtotime($medicion['time']));
                $tipoGasId = $medicion['tipoGasId']; // Usamos el valor directamente del array
    
                // Ejecutamos la consulta de inserción
                $stmt->execute([$valor, $lon, $lat, $fecha, $hora, $tipoGasId]);
            }
    
            // Confirmamos la transacción
            $this->conn->commit();
    
            return json_encode(['success' => true, 'message' => 'Mediciones insertadas correctamente']);
        } catch (PDOException $e) {
            // Si ocurre un error, hacemos rollback de la transacción
            $this->conn->rollBack();
            error_log("Error al insertar mediciones: " . $e->getMessage() . "\n", 3, $this->logFile);
            return json_encode(['success' => false, 'error' => 'Error al insertar las mediciones']);
        }
    }

    public function obtenerMedicionesAPI() {
        try {
            // Consulta SQL para obtener todas las mediciones de la tabla MEDICIONESAPI relacionadas con TIPOGAS
            $query = "
                SELECT * FROM MEDICIONESAPI;
            ";
    
            // Prepara y ejecuta la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    
            // Verifica si se encontraron resultados
            if ($stmt->rowCount() > 0) {
                $mediciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                registrarError("PORFAVORFUNCIONA");
                return json_encode(['success' => true, 'mediciones' => $mediciones]);
            } else {
                return json_encode(['success' => false, 'error' => 'No se encontraron mediciones.']);
            }
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en obtener mediciones API: " . $e->getMessage() . "\n", 3, $this->logFile);
            return json_encode(['success' => false, 'error' => 'Error al obtener las mediciones.']);
        }
    }
    

}

