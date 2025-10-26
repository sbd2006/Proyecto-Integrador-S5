@extends('admin.dashboard')

@section('title', isset($producto) ? 'Editar Producto' : 'Agregar Nuevo Producto')

@section('titulomain')
    {{-- Centramos el título visualmente --}}
    <div class="Editar Producto">
        {{ isset($producto) ? 'Editar Producto ✏️' : 'Agregar Nuevo Producto 🍰' }}
    </div>
@endsection

@section('contenido')
    <style>
        /* ✅ Todos los estilos están limitados al formulario de producto */
        .form-producto {
            max-width: 600px;
            margin: 0 auto;
            background: #f8e1e7;
            padding: 20px;
            border-radius: 10px;
        }

        .form-producto label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .form-producto input[type="text"],
        .form-producto input[type="number"],
        .form-producto input[type="file"],
        .form-producto select,
        .form-producto textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #d8a7b1;
            border-radius: 5px;
            background: #fff;
        }

        /* ✅ Botones solo del formulario */
        .form-producto button {
            background-color: #a64d79;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        .form-producto button:hover {
            background-color: #8b3f67;
        }

        .form-producto .preview {
            text-align: center;
            margin-top: 15px;
        }

        .form-producto .preview img {
            max-width: 150px;
            border-radius: 10px;
        }

        .form-producto a.btn-volver {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color: #a64d79;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .form-producto a.btn-volver:hover {
            background-color: #8b3f67;
        }

        .form-producto .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>

    {{-- Si no hay categorías, mostramos un aviso --}}
    @if($categorias->isEmpty())
        <div class="alert">
            ⚠️ No puedes crear productos porque no hay categorías registradas.<br>
            <a href="{{ route('categoria.create') }}" class="btn-volver" style="margin-top:10px; display:inline-block;">
                ➕ Crear Categoría
            </a>
        </div>
    @else
        <form 
            class="form-producto"
            action="{{ isset($producto) ? route('producto.update', $producto->id) : route('producto.store') }}" 
            method="POST" 
            enctype="multipart/form-data">

            @csrf
            @if(isset($producto))
                @method('PUT')
            @endif

            <label for="categoria_id">Categoría del Producto:</label>
            <select name="categoria_id" id="categoria_id" required>
                <option value="">-- Selecciona una categoría --</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>

            <label>Nombre del Producto:</label>
            <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>

            <label>Precio:</label>
            <input type="number" name="precio" step="0.01" value="{{ old('precio', $producto->precio ?? '') }}" required>

            <label>Precio de Venta:</label>
            <input type="number" name="precio_venta" step="0.01" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}">

            <label>Stock:</label>
            <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? '') }}" required>

            <label>Imagen del Producto:</label>
            <input type="file" name="imagen" accept="image/*">

            @if(isset($producto) && $producto->imagen)
                <div class="preview">
                    <p>Imagen actual:</p>
                    <img src="{{ asset('img/' . $producto->imagen) }}" alt="Imagen del producto">
                </div>
            @endif

            <button type="submit">{{ isset($producto) ? 'Actualizar Producto' : 'Guardar Producto' }}</button>

            <div style="text-align:center;">
                <a href="{{ route('producto.index') }}" class="btn-volver">⬅️ Volver al listado</a>
            </div>
        </form>
    @endif
@endsection
