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

}
