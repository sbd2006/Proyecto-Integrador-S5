<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .min-h-screen {
            background-color: #fff;
        }

        .estado {
            padding: 4px 10px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
        }

        .estado.pendiente {
            background-color: #f59e0b;
        }

        /* amarillo */
        .estado.en_preparacion {
            background-color: #3b82f6;
        }

        /* azul */
        .estado.listo {
            background-color: #10b981;
        }

        /* verde */
        .estado.entregado {
            background-color: #16a34a;
        }

        /* verde oscuro */
        .estado.cancelado {
            background-color: #ef4444;
        }

        /* rojo */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        <header class="">
            <div class="contenedor-nav">
                <a class="logo">
                    <img src="{{ asset('img/logotipo.jpg') }}" alt="Logo Postres">
                    <span>Postres María José</span>
                </a>

                <nav class="navegacion">
                    <ul class="ulList">
                        <a href="{{ url('/') }}">Inicio</a>
                        <a href="{{ url('/#productos') }}">Productos</a>
                        <a href="{{ url('/#about-us') }}">Nosotros</a>

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
        </header>

        <!-- Contenido principal -->
        <div class="p-6">
            @yield('contenido')
        </div>
    </div>

    <!-- Aquí cargamos los scripts -->
    @yield('scripts')
</body>

</html>