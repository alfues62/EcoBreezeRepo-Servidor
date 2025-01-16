<?php

require_once(__DIR__ . '/../../db/conexion.php');


/**
 * @class DatosCRUD
 * @brief Clase encargada de gestionar las operaciones CRUD relacionadas con las mediciones en la base de datos.
 *
 * Esta clase se encarga de realizar operaciones de lectura sobre las mediciones de los usuarios. Permite obtener
 * las mediciones asociadas a un usuario específico desde la base de datos, gestionando las consultas SQL necesarias
 * para obtener los datos de las mediciones.
 *
 *   Métodos principales:
 *       1. Obtener las mediciones asociadas a un usuario específico.
 *       2. Devolver las mediciones en formato JSON.
 *       3. Manejar posibles errores durante las consultas y registrar los errores en un archivo de log.
 *
 * @note Utiliza `PDO` para interactuar con la base de datos de manera segura y eficiente.
 * @note Esta clase solo realiza consultas de lectura sobre las mediciones sin modificar los registros existentes en la base de datos.
 * @note La clase tiene un mecanismo de manejo de errores que registra los fallos en un archivo de log especificado.
 *
 * @note **Método principal**: `obtenerMedicionesUsuario()`
 *       Este método recibe el ID de un usuario y devuelve un conjunto de mediciones asociadas a ese usuario.
 *
 * Ejemplo de uso:
 * ```php
 * $datosCRUD = new DatosCRUD($conn);
 * $result = $datosCRUD->obtenerMedicionesUsuario($usuarioId);
 * echo $result;  // Resultado en formato JSON con las mediciones o un mensaje de error.
 * ```
 */

class DatosCRUD {
    private $conn;
    private $logFile; 

    // Modificación del constructor para aceptar la conexión existente y definir el archivo de log
    public function __construct($conn) {
        $this->conn = $conn; // Usar la conexión pasada como argumento
        date_default_timezone_set('Europe/Madrid'); // Establecer la zona horaria
        $this->logFile = '/var/www/html/logs/app.log'; // Establecer la ruta del archivo de log
    }

    /**
     * Obtiene las mediciones asociadas a un usuario específico desde la base de datos.
     *
     * Este método consulta las mediciones registradas para un usuario dado utilizando su ID.
     * La información obtenida incluye detalles como valores, ubicación, fecha, hora, y tipo de gas.
     * Si no se encuentran registros o ocurre un error, se devuelve un mensaje descriptivo.
     *
     * Diseño:
     * 
     * Entrada:
     *   usuarioId (int)
     *        ---> [obtenerMedicionesUsuario()] ---> Consulta a la base de datos
     *                                              ---> Respuesta con datos o mensaje de error.
     *
     * Salida:
     *   - En caso de éxito:
     *     {
     *       "success": true,
     *       "mediciones": [
     *         {
     *           "IDMedicion": 1,
     *           "Valor": 50,
     *           "Lon": -0.376288,
     *           "Lat": 39.469907,
     *           "Fecha": "2024-12-07",
     *           "Hora": "12:00:00",
     *           "Categoria": "Alto",
     *           "TIPOGAS_TipoID": 2,
     *           "TipoGas": "CO2"
     *         },
     *         ...
     *       ]
     *     }
     *   - En caso de error o datos no encontrados:
     *     {
     *       "success": false,
     *       "error": "Mensaje descriptivo del error."
     *     }
     *
     * @param int $usuarioId El ID del usuario cuyas mediciones se desean obtener.
     * @return string Un JSON con las mediciones del usuario o un mensaje de error.
     */

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

    public function insertarMedicionesAPI($mediciones) {
        try {
            // Comenzamos la transacción para asegurar la consistencia de los datos
            $this->conn->beginTransaction();
    
            // Consulta SQL para insertar una medición en la tabla MEDICIONESAPI
            $query = "
                INSERT INTO `MEDICIONESAPI` 
                (`ValorAQI`, `CO2`, `NO2`, `O3`, `SO2`, `Lon`, `Lat`, `Fecha`, `Hora`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
    
            // Preparamos la consulta
            $stmt = $this->conn->prepare($query);
    
            // Recorremos cada medición y la insertamos en la base de datos
            foreach ($mediciones as $medicion) {
                // Asignar valores a las variables
                $valorAQI = $medicion['ValorAQI'];  // Valor AQI
                $lon = $medicion['longitude'];
                $lat = $medicion['latitude'];
                $fecha = date('Y-m-d', strtotime($medicion['time']));
                $hora = date('H:i:s', strtotime($medicion['time']));
                
                // Verificamos si los gases existen en la medición, si no, asignamos null
                $co2 = $medicion['CO2'] ?? null;   // CO₂
                $no2 = $medicion['NO2'] ?? null;   // NO₂
                $o3 = $medicion['O3'] ?? null;     // O₃
                $so2 = $medicion['SO2'] ?? null;   // SO₂
    
                // Ejecutamos la consulta de inserción
                $stmt->execute([$valorAQI, $co2, $no2, $o3, $so2, $lon, $lat, $fecha, $hora]);
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
            // Consulta SQL para obtener todas las mediciones de la tabla MEDICIONESAPI
            $query = "
                SELECT * FROM MEDICIONESAPI;
            ";
    
            // Prepara y ejecuta la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            error_log("Consulta exitosa: Se obtuvieron mediciones.\n", 3, $this->logFile);
    
            // Verifica si se encontraron resultados
            if ($stmt->rowCount() > 0) {
                $mediciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return ['success' => true, 'mediciones' => $mediciones]; // Devuelve un array PHP
            } else {
                return ['success' => false, 'error' => 'No se encontraron mediciones.']; // Devuelve un array PHP
            }
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en obtener mediciones API: " . $e->getMessage() . "\n", 3, $this->logFile);
            return ['success' => false, 'error' => 'Error al obtener las mediciones.']; // Devuelve un array PHP
        }
    }

}
