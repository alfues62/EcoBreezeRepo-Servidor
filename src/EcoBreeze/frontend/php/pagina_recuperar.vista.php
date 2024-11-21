<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/css/main.css">
    <link rel="stylesheet" href="/frontend/css/pagina_recuperar.css">
    <title>Recuperación de Contraseña</title>
</head>
<body>
    <div class="container">
        <h1>Recuperación de Contraseña</h1>

        <!-- Mostrar mensajes según el estado -->
        <?php if (!empty($message)): ?>
            <div class="message success">
                <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            
            <!-- Formulario para cambiar la contraseña -->
            <form id="change-password-form" action="/backend/recuperar_contrasena/pagina_verificar_contrasena.php" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                
                <div>
                    <label for="new-password">Nueva Contraseña:</label>
                    <input type="password" id="nuevaContrasena" name="nuevaContrasena" required>
                </div>
                <div>
                    <label for="confirm-password">Confirmar Contraseña:</label>
                    <input type="password" id="confirmarContrasena" name="confirmarContrasena" required>
                </div>
                
                <!-- Mensaje de error de contraseñas -->
                <div id="password-error" class="password-error" style="display:none; color: red; margin-top: 10px;"></div>
                
                <div>
                    <button type="submit" id="submit-btn" disabled>Cambiar Contraseña</button>
                </div>
            </form>
        
        <?php elseif (!empty($error)): ?>
            <div class="message error">
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <!-- Aviso de redirección -->
            <div id="redirection-message" class="redirection-message">
                <p>Serás redirigido a la página de inicio en 3 segundos...</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Referir al archivo JS externo -->
    <script src="/frontend/js/pagina_recuperar.js"></script>
</body>
</html>
