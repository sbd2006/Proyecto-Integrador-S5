@extends('admin.dashboard')

@section('titulomain', isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría')

@section('contenido')
    <style>
        .contenedor { max-width: 650px; margin: 0 auto; background:#fff; border:1px solid #d8a7b1; border-radius:10px; padding:20px; }
        label { display:block; margin-top:10px; font-weight:bold; }
        input[type="text"], textarea, select { width:100%; padding:10px; border:1px solid #d8a7b1; border-radius:5px; background:#fff; }
        textarea { min-height: 110px; resize: vertical; }
        .errores { background:#fff1f5; border:1px solid #d8a7b1; color:#8b3f67; padding:10px; border-radius:8px; margin-bottom:10px; }
        .help { color:#8b3f67; font-size:12px; margin-top: 4px; }
        button { background:#a64d79; color:#fff; border:none; padding:10px 15px; border-radius:5px; cursor:pointer; margin-top:15px; }
        button:hover { background:#8b3f67; }
        a.btn-volver { display:inline-block; text-decoration:none; color:#fff; background:#a64d79; padding:8px 12px; border-radius:5px; margin-top:15px; }
        a.btn-volver:hover { background:#8b3f67; }
    </style>

    <div class="contenedor">
        <h1 style="margin:0 0 12px 0;">{{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }}</h1>

        @if ($errors->any())
            <div class="errores">
                <strong>Revisa los campos:</strong>
                <ul style="margin: 6px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ isset($categoria) ? route('categoria.update', ['categoria' => $categoria]) : route('categoria.store') }}">
            @csrf
            @if(isset($categoria)) @method('PUT') @endif

            <label for="nombre">Nombre</label>
            <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required>
            <div class="help">Ej.: “Tortas”, “Galletas”, “Helados”.</div>
            @error('nombre') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
            @error('descripcion') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

            <label for="estado">Estado</label>
            @php $estadoActual = old('estado', $categoria->estado ?? 1); @endphp
            <select id="estado" name="estado">
                <option value="1" {{ (string)$estadoActual === '1' ? 'selected' : '' }}>Activa</option>
                <option value="0" {{ (string)$estadoActual === '0' ? 'selected' : '' }}>Inactiva</option>
            </select>
            @error('estado') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

            <div style="text-align:center;">
                <button type="submit">{{ isset($categoria) ? 'Actualizar Categoría' : 'Guardar Categoría' }}</button>
                <a href="{{ route('categoria.index') }}" class="btn-volver">⬅️ Volver al listado</a>
            </div>
        </form>
    </div>
@endsection
