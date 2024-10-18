<?php
// index.php

// Incluir el archivo de conexión a la base de datos
include_once 'db/db.php';  // Cambia la ruta a la correcta según la estructura del contenedor

// Verificar si se envía el número por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero'])) {
    $numero = intval($_POST['numero']);
    
    // Insertar el número en la base de datos
    $stmt = $conn->prepare("INSERT INTO acciones (numero) VALUES (:numero)");
    
    // Verifica si la preparación tuvo éxito
    if (!$stmt) {
        die("Error en la preparación: " . implode(", ", $conn->errorInfo()));
    }

    // Ejecutar la consulta con el número proporcionado
    if (!$stmt->execute([':numero' => $numero])) {
        die("Error al insertar el número: " . implode(", ", $stmt->errorInfo()));
    }
}

// Leer el último número insertado
$result = $conn->query("SELECT numero FROM acciones ORDER BY created_at DESC LIMIT 1");
$ultimoNumero = $result->fetch(PDO::FETCH_ASSOC);

// Verificar si se recibió un número para buscar
$numeroBuscado = isset($_GET['numero']) ? intval($_GET['numero']) : null;

// Buscar el número en la base de datos
$numeroEncontrado = null;
if ($numeroBuscado !== null) {
    $stmt = $conn->prepare("SELECT * FROM acciones WHERE numero = :numero");
    
    // Verifica si la preparación tuvo éxito
    if (!$stmt) {
        die("Error en la preparación: " . implode(", ", $conn->errorInfo()));
    }

    // Ejecutar la consulta con el número buscado
    $stmt->execute([':numero' => $numeroBuscado]);
    $numeroEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometría</title>
</head>
<body>
    <h1>Registrar Número</h1>
    <form method="POST">
        <input type="number" name="numero" required>
        <button type="submit">Enviar</button>
    </form>

    <h2>Último Número: <?php echo isset($ultimoNumero['numero']) ? htmlspecialchars($ultimoNumero['numero']) : 'N/A'; ?></h2>

    <h2>Buscar Número</h2>
    <form method="GET">
        <input type="number" name="numero" required>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($numeroBuscado !== null): ?>
        <h2>Resultado de la Búsqueda</h2>
        <?php if ($numeroEncontrado): ?>
            <p>Número encontrado: <?php echo htmlspecialchars($numeroEncontrado['numero']); ?></p>
        <?php else: ?>
            <p>No se encontró el número buscado.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
