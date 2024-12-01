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

    public function obtenerNotificacionesUsuario($usuarioId) {
        try {
            $query = "
                SELECT 
                    n.NotificacionID, 
                    n.Titulo, 
                    n.Cuerpo, 
                    n.Fecha
                FROM 
                    NOTIFICACION n
                INNER JOIN 
                    USUARIO u ON n.USUARIO_ID = u.ID
                WHERE 
                    u.ID = ?
                ORDER BY 
                    n.Fecha DESC;
            ";
    
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$usuarioId]);
    
            if ($stmt->rowCount() > 0) {
                $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => true, 'notificaciones' => $notificaciones]);
            } else {
                return json_encode(['success' => false, 'error' => 'No se encontraron notificaciones para el usuario.']);
            }
        } catch (PDOException $e) {
            error_log("Error en obtener notificaciones usuario: " . $e->getMessage() . "\n", 3, $this->logFile);
            return json_encode(['success' => false, 'error' => 'Error al obtener las notificaciones del usuario.']);
        }
    }
    
    public function insertarNotificacion($usuarioID, $titulo, $cuerpo, $fecha) {
        try {
            // Verificar si los parámetros son válidos antes de ejecutar
            if (empty($usuarioID) || empty($titulo) || empty($cuerpo) || empty($fecha)) {
                return ['error' => 'Todos los campos son obligatorios para insertar una notificación.'];
            }
    
            // Definir la consulta SQL con nombres explícitos de columnas
            $query = "INSERT INTO NOTIFICACION (USUARIO_ID, Titulo, Cuerpo, Fecha) VALUES (:usuario_id, :titulo, :cuerpo, :fecha)";
            
            // Preparar la consulta
            $stmt = $this->conn->prepare($query);
    
            // Asociar los parámetros con los valores de entrada
            $stmt->bindParam(':usuario_id', $usuarioID, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':cuerpo', $cuerpo, PDO::PARAM_STR);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    
            // Ejecutar la consulta
            $stmt->execute();
    
            return ['success' => 'Notificación insertada con éxito.'];
        } catch (PDOException $e) {
            // Registrar el error en el log para depuración
            logMessage("Error en insertar notificación: " . $e->getMessage());
            return ['error' => 'Error al insertar la notificación.'];
        }
    }
    
    

}
