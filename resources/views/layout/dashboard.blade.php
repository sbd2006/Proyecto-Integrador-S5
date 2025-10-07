<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel de Control') - Postres María José</title>
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
            height: 100vh;
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
            margin-bottom: 30px;
        }

        .user {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .user img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 2px solid #fff;
        }

        .user-name {
            font-weight: bold;
        }

        .menu {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .menu a {
            width: 80%;
            padding: 10px;
            margin: 8px 0;
            background-color: #c96f94;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            transition: 0.3s;
            font-size: 15px;
        }

        .menu a:hover {
            background-color: #8b3f67;
        }

        .content {
            flex: 1;
            padding: 40px;
            background-color: #fff1f5;
            overflow-y: auto;
        }

        h1 {
            color: #a64d79;
        }

        .logout {
            margin-top: auto;
            margin-bottom: 20px;
        }

        .logout a {
            background-color: #f25c77;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .logout a:hover {
            background-color: #d34b65;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">🍰 Tienda</div>

        <div class="user">
            <img src="https://cdn-icons-png.flaticon.com/512/146/146005.png" alt="Usuario">
            <div class="user-name">Admin</div>
        </div>

        <div class="menu">
            <a href="{{ route('inicio') }}">🏠 Inicio</a>
            <a href="{{ route('producto.index') }}">🍰 Productos</a>
            <a href="{{ route('categoria.index') }}">🏷️ Categorías</a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger" style="border:none; background:none; color:white;">
                Salir
            </button>
        </form>

    </div>

    <div class="content">
        <div class="titulo-nav">
            <h1>@yield('titulomain')</h1>
        </div>

        <div class="contenido">
            @yield('contenido')
        </div>
    </div>

</body>
</html>
