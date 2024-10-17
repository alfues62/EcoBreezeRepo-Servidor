<?php
date_default_timezone_set('Europe/Madrid');
$host = getenv('MYSQL_HOST');
$dbname = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero'])) {
        $numero = intval($_POST['numero']);
        $stmt = $pdo->prepare("INSERT INTO acciones (numero) VALUES (:numero)");
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "¡Número guardado correctamente!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se recibió ningún número."]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error en la conexión: " . $e->getMessage()]);
}
?>