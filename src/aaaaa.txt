<?php
date_default_timezone_set('Europe/Madrid');
// Datos de conexión a la base de datos
$host = getenv('MYSQL_HOST');
$dbname = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si se ha enviado el formulario para guardar el número
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero'])) {
        $numero = intval($_POST['numero']);
        $stmt = $pdo->prepare("INSERT INTO acciones (numero) VALUES (:numero)");
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();
        echo "<p>¡Número guardado correctamente!</p>";
    }

    // Si se ha enviado el formulario para buscar el número
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_numero'])) {
        $numero_buscar = intval($_POST['buscar_numero']);
        $stmt = $pdo->prepare("SELECT numero FROM acciones WHERE numero = :numero");
        $stmt->bindParam(':numero', $numero_buscar);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>¡Número encontrado en la base de datos: " . htmlspecialchars($resultado['numero']) . "!</p>";
        } else {
            echo "<p>El número no se encuentra en la base de datos.</p>";
        }
    }

    // Obtener el último número ingresado
    $stmt = $pdo->prepare("SELECT numero FROM acciones ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $ultimo_numero = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Verifica qué devuelve la consulta
    var_dump($ultimo_numero); // Muestra el último número obtenido

    // Mostrar el último número en la página
    if ($ultimo_numero) {
        echo "<p>Último número guardado en la base de datos: " . htmlspecialchars($ultimo_numero['numero']) . "</p>";
    } else {
        echo "<p>No hay números guardados en la base de datos.</p>";
    }

} catch (PDOException $e) {
    echo "<p>Error en la conexión: " . $e->getMessage() . "</p>";
}
?>

<!-- Formulario para teclear el número -->
<form method="POST" action="">
    <label for="numero">Introduce un número para guardar:</label>
    <input type="number" id="numero" name="numero" required>
    <button type="submit">Guardar número</button>
</form>

<!-- Formulario para buscar el número -->
<form method="POST" action="">
    <label for="buscar_numero">Buscar un número:</label>
    <input type="number" id="buscar_numero" name="buscar_numero" required>
    <button type="submit">Buscar número</button>
</form>

