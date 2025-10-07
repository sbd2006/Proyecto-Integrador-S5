<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos - Postres María José</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #d8a7b1;
            padding-bottom: 10px;
        }

        header img {
            width: 80px;
            margin-bottom: 5px;
        }

        h1 {
            color: #a64d79;
            font-size: 20px;
            margin: 0;
        }

        h2 {
            color: #a64d79;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #d8a7b1;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8e1e7;
            color: #4b1e2f;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #fdf6f8;
        }

        .footer {
            text-align: right;
            font-size: 11px;
            color: #777;
            margin-top: 30px;
            border-top: 1px solid #d8a7b1;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <header>
        {{-- Cambia la ruta del logo si lo tienes --}}
        <img src="{{ public_path('img/Logo.jpg') }}" alt="Logo de Postres María José" width="100">
        <h1>Postres María José</h1>
        <h2>Reporte de Productos</h2>
    </header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->descripcion }}</td>
                    <td>${{ number_format($p->precio, 2) }}</td>
                    <td>{{ $p->stock }}</td>
                    {{-- <td>{{ $p->categoria->nombre ?? 'Sin categoría' }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
