<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>EcoBrezze</title>
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Core theme CSS -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/index.css">
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Poppings -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>
<body>
    

<!-- Navigation -->
<nav class="navbar fixed-top">
    <div class="navbar-brand">
        <a href="#seccionInicio">
            <img src="/img/logoBio.png" alt="Logo" class="logo">
        </a>
        <span>EcoBreeze</span>
    </div>
    <ul class="navbar-nav">
        <li><a href="#seccionInicio">INICIO</a></li>
        <li><a href="#seccionProducto">PRODUCTO</a></li>
        <li><a href="#planes">PLAN</a></li>
        <li><a href="#equipo-ecobreeze">EQUIPO ECOBREZZE</a></li>
        <li><a href="#contacto">CONTACTO</a></li>
        <li><a href="/backend/login/main_login.php" class="btn btn-login">INICIAR SESIÓN</a></li>

    </ul>
</nav>





<!-- Masthead -->
<header id = "seccionInicio" class="masthead">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-xl-6 text-center text-white">
                <h1 class>A TU ALCANCE</h1>
                <h2 class>TECNOLOGÍA AVANZADA</h2>
                <h3 class>PARA CUIDAR TU SALUD RESPIRATORIA</h3>

                <form class="botonEmpiezaAhora" id="EmpiezaAhora">
                    <div class="row">
                            <button class="btn btn-primary" id="submitButton" >EMPIEZA AHORA</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>



<!-- Features Section -->
<section class="features-icons bg-light text-center">
    <div class="Features-container">
        <div class="features-icons-item">
            <h3>SENSOR CO2 O3 CH4</h3>
            <img src="img/sensor.png" alt="Sensor" class="feature-image">
        </div>
        <div class="features-icons-item">
            <h3>GEOPOSICIONAMIENTO</h3>
            <img src="img/geo.png" alt="Geoposicionamiento" class="feature-image">
        </div>
        <div class="features-icons-item">
            <h3>MONITORIZACIÓN</h3>
            <img src="img/moni.png" alt="Monitorización" class="feature-image">
        </div>
    </div>
</section>

<!-- Nuestro Producto Section -->
<section id = "seccionProducto" class="our-product">
    <h1 class="section-title">NUESTRO PRODUCTO ECO BREZZE</h1>
    <div class="container">
        <div class="product-content">
            <!-- Contenedor para la imagen y el botón -->
            <div class="product-image-button">
                <img src="img/producti.png" alt="Imagen del Producto Eco Breeze, un dispositivo para monitoreo de calidad del aire" class="product-image">
                <div class="button-container">
                    <button class="btn" id="comprarButton" aria-label="Comprar ahora el producto Eco Breeze">Comprar ahora</button>
                </div>
            </div>
            
            <!-- Contenedor para los detalles del producto -->
            <div class="product-details">
                <section class="product-feature">
                    <div class="titles">
                        <h2>SENSOR</h2>
                        <h3>CO2</h3>
                    </div>
                    <p>El CO₂ en espacios cerrados puede acumularse rápidamente, afectando la concentración y causando fatiga. Monitorearlo ayuda a mejorar la ventilación, la calidad de vida y el rendimiento.</p>
                </section>
                <section class="product-feature">
                    <div class="titles">
                        <h2>SENSOR</h2>
                        <h3>CH4</h3>
                    </div>
                    <p>El metano es un gas inflamable que, en altos niveles, desplaza el oxígeno y aumenta el riesgo de explosión. Su monitoreo en industrias y agricultura mejora la seguridad y ayuda a combatir el cambio climático.</p>
                </section>
                <section class="product-feature">
                    <div class="titles">
                        <h2>SENSOR</h2>
                        <h3>O3</h3>
                    </div>
                    <p>El ozono es un contaminante perjudicial que puede irritar ojos, garganta y vías respiratorias, empeorando afecciones como el asma y problemas pulmonares, especialmente en niños, ancianos y personas con enfermedades respiratorias crónicas.</p>
                </section>
            </div>
        </div>
    </div>
</section>




<!-- Planes Section -->
<section id="planes" class="text-center">
    <h1 class="section-title">PLANES</h1>
    <p>EcoBreeze es una solución integral para monitorear la calidad del aire, compuesta por un dispositivo avanzado de sensores y una aplicación móvil intuitiva. Nuestro dispositivo cuenta con sensores de alta precisión para medir niveles de CO₂, O₃, CH₄, permitiéndote saber en tiempo real la calidad del aire que respiras.</p>
    <div class="container-planes">
        <!-- Plan Gratuito -->
        <div class="plan-container">
            <div class="plan">
                <h3>Plan 
                <span style="color: #4ca5aa;">GRATUITO</span></h3>
                <ul>
                    <li>1 cuenta</li>
                    <li>Mapa calidad del aire</li>
                    <li>Actualizaciones limitadas</li>
                    <li><s>Acceso App Móvil</s></li>
                </ul>
                <a href="backend/login/main_login.php" class="btn">REGÍSTRATE</a></li>
            </div>
        </div>

        <!-- Línea de separación -->
        <div class="separador"></div>

        <!-- Plan Premium -->
        <div class="plan-container">
            <div class="plan">
                <h3>Plan <span style="color: #4ca5aa;">PREMIUM</span> 0,99€</h3>
                <ul>
                    <li>Dispositivo EcoBreeze</li>
                    <li>Acceso App Móvil</li>
                    <li>Registro de Datos Completo</li>
                    <li>Mediciones en Tiempo Real</li>
                </ul>
                <button class="btn" type="button">COMPRAR</button>
            </div>
        </div>
    </div>
</section>



<!-- Equipo EcoBreeze Section -->
<section id="equipo-ecobreeze" class="text-center">
    <!-- Título de la sección -->
    <h1 class="section-title">EQUIPO ECOBREZZE</h1>
    
    <div class="container">
        <!-- Contenido principal de la sección -->
        <div class="row">
            <!-- Columna izquierda: texto introductorio y la imagen -->
            <div class="col-md-6 text-left">
                <p>
                    En EcoBreeze nos dedicamos a concienciar a las comunidades sobre la contaminación y la calidad del aire que respiran, ya sea en sus hogares, trabajos o en sus entornos cotidianos. A través de la <strong>tecnología avanzada y el análisis de datos</strong>, ayudamos a organizaciones, ciudadanos y gobiernos a entender el impacto de la contaminación atmosférica y a actuar con base en esa información.
                </p>
                <img src="img/equipo.png" alt="Equipo de EcoBreeze trabajando" class="img-fluid mt-3" />
            </div>

            <!-- Columna derecha: lista de departamentos -->
            <div class="col-md-6 text-left">
                <h3>Departamento de Investigación, Desarrollo e Innovación</h3>
                <p>(I+D+i), dirigido por Hao, se encarga de impulsar la innovación y la creación de nuevas soluciones tecnológicas.</p>

                <h3>Departamento de Software</h3>
                <p>Bajo la dirección de Alex, se enfoca en desarrollar las plataformas digitales y algoritmos que analizan y visualizan los datos de calidad del aire.</p>

                <h3>Departamento de Diseño Gráfico</h3>
                <p>Liderado por Estela, asegura que nuestras soluciones sean intuitivas y atractivas para nuestros usuarios.</p>

                <h3>Departamento de Hardware</h3>
                <p>Dirigido por Aleix, diseña y construye los sensores y dispositivos que recolectan datos precisos sobre la calidad del aire.</p>

                <h3>Departamento de Investigación Medioambiental</h3>
                <p>Con la directora Lola a la cabeza, analiza el impacto de los datos obtenidos, ofreciendo estudios y recomendaciones para la mejora de la salud pública y ambiental.</p>
            </div>
        </div>
    </div>
</section>


<!-- Sección de Contacto -->
<section id="contacto" class="contact-section">
    <!-- Título de la sección -->
    <h1 class="section-title">Ponte en contacto con nuestro equipo:</h1>
    <!-- Subtítulo centrado debajo del título -->
    <p id="textoArriba">Si tienes dudas, o algún problema con nuestro dispositivo ¡háznoslo saber!</p>
    <div id="contendorContacto">
        <div class="row">
            <!-- Información de Contacto -->
            <div class="col-md-6 text-left contact-info">
                <h3>Utiliza las siguientes vías de contacto, o rellena el formulario.</h3>
                <p>Vía E-mail</p>
                <a href="mailto:EcoBrezze@gmail.com">EcoBrezze@gmail.com</a>
                <p>En nuestras redes sociales</p>
                <a href="https://twitter.com/EcoBrezze" target="_blank">@EcoBrezze</a>
                <p>Por teléfono</p>
                <a href="tel:911234567">91-1234-567</a>
            </div>
            <!-- Formulario de Contacto -->
            <div class="col-md-6">
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Escribe tu nombre" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Escribe tu e-mail" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Escribe tu teléfono (Opcional)">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Escribe tu mensaje" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit">ENVIAR MENSAJE</button>
                </form>
            </div>
        </div>
    </div>
</section>



    <!-- Footer -->
    <footer class="footer bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-start my-auto">
                    <ul class="list-inline mb-2">
                        <li class="list-inline-item"><a href="#!">About</a></li>
                        <li class="list-inline-item">⋅</li>
                        <li class="list-inline-item"><a href="#!">Contact</a></li>
                        <li class="list-inline-item">⋅</li>
                        <li class="list-inline-item"><a href="#!">Terms of Use</a></li>
                        <li class="list-inline-item">⋅</li>
                        <li class="list-inline-item"><a href="#!">Privacy Policy</a></li>
                    </ul>
                    <p class="text-muted small mb-4 mb-lg-0">&copy; Your Website 2024. All Rights Reserved.</p>
                </div>
                <div class="col-lg-6 text-center text-lg-end my-auto">
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="Twitter"><i class="bi-twitter"></i></a>
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="Facebook"><i class="bi-facebook"></i></a>
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="Instagram"><i class="bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS -->
    <script src="js/scripts.js"></script>
</body>
</html>
