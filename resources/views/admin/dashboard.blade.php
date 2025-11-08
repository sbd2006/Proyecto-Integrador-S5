<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel de Control') - Postres María José</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #fff9fb;
            color: #4b1e2f;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #a64d79;
            color: white;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .logo {
            background: #d8a7b1;
            border-radius: 10px;
            width: 200px;
            height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 20px;
            color: #4b1e2f;
            margin-bottom: 20px;
            text-align: center;
        }

        .user {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .user img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 8px;
            border: 2px solid #fff;
        }

        .user-name {
            font-weight: bold;
            text-align: center;
        }

        .menu {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 8px;
        }

        .menu a {
            width: 84%;
            padding: 10px;
            margin: 6px 0;
            background-color: #c96f94;
            color: white;
            border: none;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            transition: 0.2s;
            font-size: 15px;
            display: block;
        }

        .menu a:hover {
            background-color: #8b3f67;
        }

        .content {
            flex: 1;
            padding: 28px;
            background-color: #fff1f5;
            overflow-y: auto;
            min-height: 100vh;
        }

        h1 {
            color: #a64d79;
            margin: 0 0 12px 0;
        }

        .logout {
            margin-top: auto;
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .logout button {
            background-color: #f25c77;
            color: white;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .logout button:hover {
            background-color: #d34b65;
        }

        .bienvenida {
            text-align: center;
            background-color: #ffe5ef;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 0 10px rgba(166, 77, 121, 0.2);
        }

        .bienvenida img {
            width: 150px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .bienvenida h2 {
            color: #a64d79;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .bienvenida p {
            font-size: 16px;
            color: #5b2b43;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto;
        }

        .bienvenida small {
            display: block;
            margin-top: 20px;
            color: #8b3f67;
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">
            🍰 Tienda<br>
            <small style="font-size:12px;color:#4b1e2f">Postres María José</small>
        </div>

        <div class="user">
            <img src="https://cdn-icons-png.flaticon.com/512/146/146005.png" alt="Usuario">
            <div class="user-name">
                @auth
                {{ Auth::user()->name }}
                @php
                $isAdmin = false;
                if(auth()->check()){
                $user = auth()->user();
                if(isset($user->rol) && $user->rol === 'admin') $isAdmin = true;
                elseif(method_exists($user, 'hasRole') && $user->hasRole('admin')) $isAdmin = true;
                }
                @endphp
                @if($isAdmin)
                <div style="font-size:12px; opacity:0.9">(Administrador)</div>
                @endif
                @else
                Invitado
                @endauth
            </div>
        </div>

        <div class="menu">
            <a href="{{ route('inicio') }}">🏠 Inicio</a>

            {{-- Enlace Pedidos visible solo para admin --}}
            @if(auth()->check() && $isAdmin)
            <a href="{{ route('admin.pedidos') }}">🧾 Pedidos</a>
            @endif

            <a href="{{ route('producto.index') }}">🍰 Productos</a>
            <a href="{{ route('venta.index') }}">🏷️ Ventas</a>

            {{-- 📊 Reporte de ventas (admin) --}}
            @if(auth()->check() && $isAdmin && Route::has('reportes.ventas.resumen'))
            <a href="{{ route('reportes.ventas.resumen') }}">📊 Reporte de ventas</a>
            @endif

        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout">
            @csrf
            <button type="submit">Salir</button>
        </form>
    </div>

    <div class="content">
        {{-- ✅ Si la vista hija tiene contenido, se muestra; si no, se muestra el letrero de bienvenida --}}
        @hasSection('contenido')
        <div class="titulo-nav">
            <h1>@yield('titulomain')</h1>
        </div>

        <div class="contenido">
            @yield('contenido')
        </div>
        @else
        <div class="bienvenida">
            <img src="{{ asset('img/Logo.jpg') }}" alt="Logo Postres María José">
            <h2>¡Bienvenido(a) a Postres María José! 💕</h2>
            <p>
                Somos especialistas en los más irresistibles <strong>merengones artesanales</strong>,
                preparados con frutas frescas, crema batida y ese toque dulce que enamora.
                Cada creación es una explosión de sabor, color y alegría, perfecta para compartir y endulzar tus días.
                Descubre por qué nuestros merengones son el corazón de Postres María José. 💖
            </p>
            <small>Postres María José — Endulzando tus momentos desde 2020 🍓</small>
        </div>
        @endif
    </div>

    @yield('scripts')
</body>

</html>