
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - EcoBreeze</title>
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Core theme CSS -->
    <link href="css/stylesLola.css" rel="stylesheet" />    
    <link href="css/main.css" rel="stylesheet" />
    <link href="css/login.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/pasarela.css">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- JS -->
    <script src="js/scripts.js"></script>
<body>

<!-- Navigation -->
<nav class="navbar fixed-top">
    <div class="navbar-brand">
        <a href="#seccionInicio">
            <img src="../frontend/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="index.html" class="btn btn-volver">VOLVER</a></li>
        
    </ul>
</nav>

<!-- Pasarela Section -->
<section class="container pasarela-container">
    <h1>Realiza tu compra</h1>
    <h2>¡PRONTO TENDRÁS TU DISPOSITIVO ECOBREZZE!</h2>
    <form action="">
        <div class="row">
            <div class="col">
                <div class="inputBox">
                    <input type="text" placeholder="Nombre y apellidos">
                </div>
                <div class="inputBox">
                    <input type="email" placeholder="Correo electrónico">
                </div>
                <div class="inputBox">
                    <input type="text" placeholder="Direccion de entrega">
                </div>
                <div class="inputBox">
                    <input type="text" placeholder="Ciudad">
                </div>

                <div class="flex">
                    <div class="inputBox">

                        <input type="text" placeholder="País">
                    </div>
                    <div class="inputBox">

                        <input type="text" placeholder="Codigo postal">
                    </div>
                </div>

            </div>
            <div class="col">
                <div class="inputBox">
                    <img src="assets/img/card_img.png" alt="">
                </div>
                <div class="inputBox">
                    <input type="text" placeholder="Nombre del titular">
                </div>
                <div class="inputBox">
                    <input type="number" placeholder="Numero de tarjeta">
                </div>

                <div class="inputBox">
                    <input type="number" placeholder="Fecha de vencimiento (MM/AA)">
                </div>
                <div class="inputBox">
                    <input type="text" placeholder="Código de seguridad">
                </div>
            </div>

            
    
        </div>
        <input type="submit" value="CONFIRMAR COMPRA" class="btn submit-btn">

    </form>
</div>    
</section>

</body>
</html>