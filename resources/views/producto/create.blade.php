@extends('layout.dashboard')

@section('titulomain')
    {{-- Centramos el título visualmente --}}
    <div class="titulo-centrado">
        {{ isset($producto) ? 'Editar Producto ✏️' : 'Agregar Nuevo Producto 🍰' }}
    </div>
@endsection

@section('contenido')
    <style>
        .titulo-centrado {
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: bold;
            color: #a64d79;
        }

        form {
            max-width: 600px;
            margin: 0 auto; /* Centra el formulario */
            background: #f8e1e7;
            padding: 20px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #d8a7b1;
            border-radius: 5px;
            background: #fff;
        }

        button {
            background-color: #a64d79;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background-color: #8b3f67;
        }

        .preview {
            text-align: center;
            margin-top: 15px;
        }

        .preview img {
            max-width: 150px;
            border-radius: 10px;
        }

        a.btn-volver {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color: #a64d79;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 15px;
        }

        a.btn-volver:hover {
            background-color: #8b3f67;
        }
    </style>

    <form 
        action="{{ isset($producto) ? route('producto.update', $producto->id) : route('producto.store') }}" 
        method="POST" 
        enctype="multipart/form-data">

        @csrf
        @if(isset($producto))
            @method('PUT')
        @endif

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
@endsection
