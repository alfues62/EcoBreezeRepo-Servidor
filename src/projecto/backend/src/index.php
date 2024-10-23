<?php
// Incluir la conexión y la clase DatosCRUD
require_once '../db/conexion.php'; // Asegúrate de que la ruta sea correcta
require_once '../controllers/datos_CRUD.php'; // Asegúrate de que la ruta sea correcta

$datosCRUD = new DatosCRUD();
$mediciones = $datosCRUD->leer(); // Obtener todas las mediciones

$medicionBuscada = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['idMedicion'])) {
    $idMedicion = intval($_POST['idMedicion']);
    $mediciones = array_filter($mediciones, function($medicion) use ($idMedicion) {
        return $medicion['IDMedicion'] === $idMedicion;
    });
    if (empty($mediciones)) {
        $mediciones = ['error' => 'No se encontró la medición con esta ID.'];
    } else {
        $medicionBuscada = reset($mediciones); // Obtiene la primera medición encontrada
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediciones EcoBreeze</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Mediciones EcoBreeze</h1>

        <!-- Formulario para buscar una medición por ID -->
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="idMedicion">Ingrese la ID de la Medición:</label>
                <input type="number" name="idMedicion" id="idMedicion" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Buscar Medición</button>
        </form>

        <!-- Tabla de Mediciones -->
        <?php if (!empty($mediciones) && !isset($mediciones['error'])): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID Medición</th>
                        <th>Valor</th>
                        <th>Longitud</th>
                        <th>Latitud</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>ID Tipo Gas</th>
                        <th>ID Umbral</th>
                        <th>ID Sensor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($medicionBuscada): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medicionBuscada['IDMedicion']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['Valor']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['Lon']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['Lat']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['Fecha']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['Hora']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['TIPOGAS_TipoID']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['UMBRAL_ID']); ?></td>
                            <td><?php echo htmlspecialchars($medicionBuscada['SENSOR_ID_Sensor']); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center"><?php echo htmlspecialchars($mediciones['error']); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning mt-3">No se encontraron mediciones o ocurrió un error al intentar leer los datos.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
