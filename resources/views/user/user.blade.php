<!DOCTYPE html>
<html lang="es">
<style>
    .btn-carrito {
        background-color: #a64d79;
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.2s;
        position: relative;
    }

    .btn-carrito:hover {
        background-color: #8b3f67;
    }

    .badge {
        background-color: #fff;
        color: #a64d79;
        border-radius: 50%;
        padding: 3px 7px;
        font-size: 0.9rem;
        margin-left: 5px;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-pink-50 text-gray-900 min-h-screen">
    <header style="text-align:right; padding:15px 25px; background:#fff0f5;">
        <nav class="bg-white shadow p-4 flex justify-between items-center">
            <h1>
                <a href="{{route('productos.index')}}" class="text-xl font-bold text-pink-700">🍰 Tienda Dulce</a>
            </h1>
            <a href="{{ route('carrito.index') }}" class="btn-carrito">
                🛒 Carrito
                @if(session('carrito'))
                <span class="badge">{{ count(session('carrito')) }}</span>
                @endif
            </a>
        </nav>
    </header>

    <main class="p-6">
        @yield('content')
    </main>
</body>

</html>