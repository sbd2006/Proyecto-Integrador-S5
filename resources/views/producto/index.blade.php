@extends('layout.dashboard')

@section('titulomain', 'Gestión de Productos 🍰')

@section('contenido')
    <style>
        h1 {
            color: #a64d79;
            text-align: center;
        }

        .filtros, .acciones {
            margin-bottom: 20px;
            text-align: center;
        }

        .filtros form {
            display: inline-block;
            padding: 10px 20px;
            background: #f8e1e7;
            border-radius: 8px;
        }

        .filtros label {
            margin-right: 10px;
        }

        .filtros input, .filtros select {
            padding: 5px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #d8a7b1;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f8e1e7;
        }

        tr:nth-child(even) {
            background-color: #fff1f5;
        }

        a.btn {
            display: inline-block;
            padding: 6px 12px;
            background-color: #a64d79;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
        }

        a.btn:hover {
            background-color: #8b3f67;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        img {
            border-radius: 5px;
        }

        .acciones {
            text-align: center;
        }

        form.eliminar {
            display: inline;
        }
    </style>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- FILTROS --}}
    <div class="filtros">
        <form method="GET" action="{{ route('producto.index') }}">
            <label>Buscar por nombre:</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Ej: torta de chocolate">

            <label>Stock:</label>
            <select name="stock">
                <option value="">-- Todos --</option>
                <option value="con" {{ request('stock') == 'con' ? 'selected' : '' }}>Con stock</option>
                <option value="sin" {{ request('stock') == 'sin' ? 'selected' : '' }}>Sin stock</option>
            </select>

            <button type="submit">🔍 Filtrar</button>
            <a href="{{ route('producto.index') }}" class="btn">🔄 Limpiar</a>
        </form>
    </div>

    {{-- ACCIONES --}}
    <div class="acciones">
        <a href="{{ route('producto.create') }}" class="btn">➕ Agregar Producto</a>
        <a href="{{ route('producto.pdf') }}" class="btn" target="_blank">🧾 Generar PDF</a>
    </div>

    {{-- TABLA DE PRODUCTOS --}}
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categoría</th> {{-- ✅ NUEVA COLUMNA --}}
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>
                        @if($p->imagen)
                            <img src="{{ asset('img/' . $p->imagen) }}" width="70">
                        @else
                            Sin imagen
                        @endif
                    </td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->descripcion }}</td>
                    <td>{{ $p->categoria->nombre ?? 'Sin categoría' }}</td> {{-- ✅ Muestra la categoría --}}
                    <td>${{ number_format($p->precio_venta, 2) }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        <a href="{{ route('producto.edit', $p->id) }}" class="btn">✏️ Editar</a>
                        <form action="{{ route('producto.destroy', $p->id) }}" method="POST" class="form-eliminar" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background-color:#f25c77;">🗑️ Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay productos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINACIÓN --}}
    <div style="text-align:center; margin-top:20px;">
        {{ $productos->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.form-eliminar');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const confirmBox = document.createElement('div');
                    confirmBox.innerHTML = `
                        <div style="
                            position: fixed; 
                            top: 0; left: 0; right: 0; bottom: 0; 
                            background: rgba(0,0,0,0.5);
                            display: flex; 
                            justify-content: center; 
                            align-items: center;
                            z-index: 1000;
                        ">
                            <div style="
                                background: #fff;
                                padding: 20px;
                                border-radius: 10px;
                                text-align: center;
                                color: #4b1e2f;
                                font-family: Arial;
                                width: 300px;
                            ">
                                <p>¿Seguro que deseas eliminar este producto?</p>
                                <button id="btnSi" style="background:#a64d79; color:#fff; border:none; padding:8px 15px; border-radius:5px; margin-right:5px;">Sí, eliminar</button>
                                <button id="btnNo" style="background:#ccc; border:none; padding:8px 15px; border-radius:5px;">Cancelar</button>
                            </div>
                        </div>
                    `;

                    document.body.appendChild(confirmBox);

                    confirmBox.querySelector('#btnSi').onclick = () => {
                        form.submit();
                    };

                    confirmBox.querySelector('#btnNo').onclick = () => {
                        confirmBox.remove();
                    };
                });
            });
        });
    </script>
@endsection
