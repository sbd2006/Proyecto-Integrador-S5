<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postres María José</title>
    <link rel="icon" href="{{ asset('img/icono1.png') }}" type="image/png">

    <!-- ✅ Evita error del fetch -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="btn btn-light">Registrarse</a>
                    @endguest

                    @auth
                        @if (Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Panel Admin</a>
                        @elseif (Auth::user()->hasRole('user'))
                            <a href="{{ route('cliente.pedidos') }}" class="btn btn-light" id="btnMisPedidos">
                                🧾 Mis pedidos
                                <span id="contadorPedidos"
                                    style="background:#f25c77;color:white;border-radius:50%;padding:3px 7px;font-size:0.8rem;display:none;">0</span>
                            </a>
                            <a href="#" id="btnPerfil" class="menu-link">Perfil</a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-light">Cerrar sesión</button>
                        </form>
                    @endauth

                    <li>@livewire("icono-carrito")</li>
                </ul>
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
        <!-- Productos -->
        <section class="container" id="productos">
            <h2>Productos</h2>

            <button class="flecha flecha-izq">&#10094;</button>
            <button class="flecha flecha-der">&#10095;</button>

            <div class="productos-grid">
                @livewire("catalogo-productos")
            </div>
        </section>

        <!-- Sobre Nosotros -->
        <section class="container" id="about-us">
            <h2>Sobre Nosotros</h2>
            <div class="contenedor-sobre-nosotros">
                <img class="imagen-sobre-nosotros" src="{{ asset('img/Sob.jpg') }}" alt="sobre-nosotros">
                <div class="contenido-textos">
                    <h3><span>1</span> Pasión por los postres</h3>
                    <p>
                        En Postres María José nos dedicamos a crear experiencias dulces que alegran el corazón.
                        Cada postre es preparado con dedicación y recetas tradicionales.
                    </p>

                    <h3><span>2</span> Atención personalizada</h3>
                    <p>
                        Nos encanta consentir a nuestros clientes, por eso ofrecemos atención cálida y personalizada
                        en cada pedido. ¡Tu felicidad es nuestra misión!
                    </p>
                </div>
            </div>
        </section>

        <!-- Testimonios -->
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

    <!-- Modal de perfil -->
    <div id="modalPerfil" class="modal-overlay hidden">
        <div class="modal-content">
            <button id="cerrarModalPerfil" class="cerrar-modal">✖</button>
            <h2>Editar Perfil</h2>

            <form id="formPerfil" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="campo">
                    <label>Nombre:</label>
                    <input type="text" name="name" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                </div>

                <div class="campo">
                    <label>Email:</label>
                    <input type="email" name="email" value="{{ Auth::check() ? Auth::user()->email : '' }}" required>
                </div>

                <div class="campo">
                    <label>Foto de Perfil:</label>
                    <input type="file" name="photo" accept="image/*">
                </div>

                <div class="campo">
                    <label>Nueva contraseña (opcional):</label>
                    <input type="password" name="password">
                </div>

                <div class="campo">
                    <label>Confirmar contraseña:</label>
                    <input type="password" name="password_confirmation">
                </div>

                <button type="submit" class="btn-guardar">Actualizar Perfil</button>
            </form>
        </div>
    </div>

    {{-- ========================= --}}
    {{--        ESTILOS EXTRA     --}}
    {{-- ========================= --}}
    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .hidden {
            display: none;
        }

        .modal-content {
            background: #fffafc;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-content h2 {
            text-align: center;
            color: #a64d79;
            margin-bottom: 1.5rem;
        }

        .cerrar-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .campo {
            margin-bottom: 1rem;
        }

        .campo label {
            display: block;
            font-weight: bold;
            color: #4b1e2f;
            margin-bottom: 5px;
        }

        .campo input {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn-guardar {
            width: 100%;
            background-color: #a64d79;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-guardar:hover {
            background-color: #8b3f67;
        }
    </style>

    {{-- ========================= --}}
    {{--         SCRIPTS JS       --}}
    {{-- ========================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalPerfil');
            const abrir = document.getElementById('btnPerfil');
            const cerrar = document.getElementById('cerrarModalPerfil');
            const form = document.getElementById('formPerfil');
            const contador = document.getElementById('contadorPedidos');

            // === Modal Perfil ===
            abrir?.addEventListener('click', () => modal.classList.remove('hidden'));
            cerrar?.addEventListener('click', () => modal.classList.add('hidden'));

            form?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = new FormData(form);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) throw new Error('CSRF token no encontrado.');

                    const response = await fetch("{{ route('perfil.update') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: data
                    });

                    if (!response.ok) throw new Error("Error HTTP " + response.status);
                    const result = await response.json();

                    if (result.success) {
                        alert('✅ Perfil actualizado correctamente.');
                        modal.classList.add('hidden');
                        location.reload();
                    } else {
                        alert('⚠️ Ocurrió un error al actualizar el perfil.');
                    }
                } catch (error) {
                    console.error(error);
                    alert('❌ Error al enviar los datos. Revisa la consola.');
                }
            });

            // === Contador de pedidos ===
            async function actualizarContadorPedidos() {
                try {
                    const res = await fetch('{{ route("cliente.pedidos.cantidad") }}');
                    if (!res.ok) throw new Error('Error al obtener cantidad de pedidos');
                    const data = await res.json();

                    if (data.cantidad > 0) {
                        contador.style.display = 'inline-block';
                        contador.textContent = data.cantidad;
                    } else {
                        contador.style.display = 'none';
                    }
                } catch (err) {
                    console.error('❌ Error al actualizar contador de pedidos:', err);
                }
            }

            // Cargar y refrescar contador
            actualizarContadorPedidos();
            setInterval(actualizarContadorPedidos, 10000);

            // Escucha evento personalizado cuando se crea un pedido
            window.addEventListener('pedidoCreado', actualizarContadorPedidos);
        });
    </script>
</body>

</html>
