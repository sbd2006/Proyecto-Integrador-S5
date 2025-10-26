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
        .estado {
            padding: 4px 10px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
        }
        .estado.pendiente { background-color: #f59e0b; }       /* amarillo */
        .estado.en_preparacion { background-color: #3b82f6; }  /* azul */
        .estado.listo { background-color: #10b981; }           /* verde */
        .estado.entregado { background-color: #16a34a; }       /* verde oscuro */
        .estado.cancelado { background-color: #ef4444; }       /* rojo */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
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
        @include('layouts.navigation')

        <!-- Encabezado -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Contenido principal -->
        <main class="p-6">
            @yield('contenido')
        </main>
    </div>

    <!-- Aquí cargamos los scripts -->
    @yield('scripts')
</body>
</html>
