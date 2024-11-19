<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBreeze - Recuperación de Contraseña</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="pagina_recuperar.css">
</head>
<body>
    <div class="container">
        <h1>EcoBreeze</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para recuperar la contraseña -->
        <form action="procesar_recuperacion.php" method="POST" id="recuperar-form">
            <label for="nueva_contraseña">Nueva Contraseña:</label><br>
            <input type="password" id="nueva_contraseña" name="nueva_contraseña" required><br><br>

            <label for="confirmar_contraseña">Confirmar Contraseña:</label><br>
            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required><br><br>

            <input type="submit" value="Restablecer Contraseña">
        </form>
    </div>

    <script src="pagina_recuperar.js"></script>
</body>
</html>
