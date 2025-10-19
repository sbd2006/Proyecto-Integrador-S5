<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postres María José</title>
    <link rel="icon" href="{{ asset('img/icono1.png') }}" type="image/png">

    {{-- CSS y JS desde resources compilados por Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="header">
        <nav class="navbar">
            <!-- Botón para modo oscuro -->
            <button id="btnDarkMode">☀️</button>
        </nav>
        <div class="contenedor-nav">
            <a class="logo">
                <img src="{{ asset('img/logotipo.jpg') }}" alt="Logo Postres">
                <span>Postres María José</span>
            </a>

            <nav class="navegacion">
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#productos">Productos</a></li>
                    <li><a href="#about-us">Nosotros</a></li>

                    @guest
                    <!-- Solo se muestran si el usuario NO ha iniciado sesión -->
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-light">Registrarse</a>
                    @endguest

                    @auth
                    <!-- Si el usuario ha iniciado sesión -->
                    @if(Auth::user()->rol === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Panel Admin</a>
                    @elseif(Auth::user()->rol === 'usuario')
                    <a href="{{ route('usuario.dashboard') }}" class="btn btn-light">Panel Usuario</a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">Cerrar sesión</button>
                    </form>
                    @endauth
            </nav>
        </div>

        <div class="textos-header">
            <h1>Bienvenidos a Postres María José</h1>
            <p>Deliciosos postres caseros hechos con amor y los mejores ingredientes. ¡Endulza tus momentos con nosotros!</p>
        </div>
    </header>

    <main>
        <section class="container" id="productos">
            <h2>Productos</h2>

            <button class="flecha flecha-izq">&#10094;</button>
            <button class="flecha flecha-der">&#10095;</button>

            <div class="productos-carrusel">
                <div class="producto">
                    <img src="{{ asset('img/meren.jpg') }}" alt="Merengon">
                    <h3>Merengon personal</h3>
                    <p>$14.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/mediano.jpg') }}" alt="Merengon">
                    <h3>Merengon mediano</h3>
                    <p>$50.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/grande.jpg') }}" alt="Merengon">
                    <h3>Merengon grande</h3>
                    <p>$60.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/sencilla.jpg') }}" alt="Sencilla">
                    <h3>Oblea sencilla</h3>
                    <p>$8.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/oblea.jpg') }}" alt="Oblea">
                    <h3>Oblea doble</h3>
                    <p>$10.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/postre de milo.jpg') }}" alt="Postre de Milo">
                    <h3>Postre de milo</h3>
                    <p>$8.500</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/arroz.jpg') }}" alt="Arroz con leche">
                    <h3>Postre de arroz de leche</h3>
                    <p>$8.500</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/maracu.jpg') }}" alt="Maracuya">
                    <h3>Postre de maracuya</h3>
                    <p>$8.500</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/natas.jpg') }}" alt="Natas">
                    <h3>Postre de natas</h3>
                    <p>$8.500</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/fresas.jpg') }}" alt="Fresas con crema">
                    <h3>Fresas con crema</h3>
                    <p>$14.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/torta ch.jpg') }}" alt="Chocolate">
                    <h3>Torta de chocolate</h3>
                    <p>$60.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/pch.jpg') }}" alt="Chocolate">
                    <h3>Porción de chocolate</h3>
                    <p>$9.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/torta geno.jpg') }}" alt="Genovesa">
                    <h3>Torta de genovesa</h3>
                    <p>$60.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/genove.jpg') }}" alt="Genovesa">
                    <h3>Porción de genovesa</h3>
                    <p>$9.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/chess fresa.jpg') }}" alt="Fresa">
                    <h3>Cheesecake de fresa</h3>
                    <p>$9.000</p>
                </div>
                <div class="producto">
                    <img src="{{ asset('img/chessmora.jpg') }}" alt="Mora">
                    <h3>Cheesecake de mora</h3>
                    <p>$9.000</p>
                </div>
            </div>
        </section>

        <!-- sobre nosotros -->
        <section class="container" id="about-us">
            <h2>Sobre Nosotros</h2>
            <div class="contenedor-sobre-nosotros">
                <img class="imagen-sobre-nosotros" src="{{ asset('img/Sob.jpg') }}" alt="sobre-nosotros">
                <div class="contenido-textos">
                    <h3><span>1</span> Pasión por los postres</h3>
                    <p>En Postres María José nos dedicamos a crear experiencias dulces que alegran el corazón. Cada postre es
                        preparado con dedicación y recetas tradicionales.</p>

                    <h3><span>2</span> Atención personalizada</h3>
                    <p>Nos encanta consentir a nuestros clientes, por eso ofrecemos atención cálida y personalizada en cada
                        pedido. ¡Tu felicidad es nuestra misión!</p>
                </div>
            </div>
        </section>

        <!-- testimonios -->
        <section class="container">
            <h2>Lo que dicen nuestros clientes</h2>
            <div class="carrucel">
                <div class="carrucel-track">
                    <div class="testimonio">
                        <p>"El mejor pastel de chocolate que he probado en mi vida."</p>
                        <cite>Juan Pérez</cite>
                    </div>
                    <div class="testimonio">
                        <p>"Los postres son deliciosos y la atención increíble."</p>
                        <cite>Ana López</cite>
                    </div>
                    <div class="testimonio">
                        <p>"Perfectos para cualquier celebración, siempre los recomiendo."</p>
                        <cite>Pedro Ramírez</cite>
                    </div>
                    <div class="testimonio">
                        <p>"Excelente calidad y precios justos, 100% recomendados."</p>
                        <cite>Laura García</cite>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2025 Postres María José. Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="#">Política de privacidad</a>
                <a href="#">Términos y condiciones</a>
                <a href="#">Contacto</a>
            </div>
        </div>
    </footer>
</body>

</html>