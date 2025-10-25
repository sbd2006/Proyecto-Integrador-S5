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
                <ul class="ulList">
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#productos">Productos</a></li>
                    <li><a href="#about-us">Nosotros</a></li>

                    @guest
                    <!-- Solo se muestran si el usuario NO ha iniciado sesión -->
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-light">Registrarse</a>
                    @endguest

                    
                    @auth
                    @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Panel Admin</a>
                    @endif
                    
                    
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">Cerrar sesión</button>
                    </form>
                    @endauth
                    <li>@livewire("icono-carrito")</li>
            </nav>
        </div>

        <div class="textos-header">
            <h1>Bienvenidos a Postres María José</h1>
            <center>
                <p>Deliciosos postres caseros hechos con amor y los mejores ingredientes.</p>
                <p>¡Endulza tus momentos con nosotros!</p>
            </center>
        </div>
    </header>

    <main>
        <section class="container" id="productos">
            <h2>Productos</h2>

            <button class="flecha flecha-izq">&#10094;</button>
            <button class="flecha flecha-der">&#10095;</button>

            <div class="productos-grid">
                @livewire("catalogo-productos")
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