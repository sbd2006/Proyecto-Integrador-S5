<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ (request()->route('categoria') ? 'Editar Categoría' : 'Agregar Categoría') }} - Postres María José</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { font-family: Arial, sans-serif; background:#fff9fb; color:#4b1e2f; margin:20px; }
        h1   { color:#a64d79; text-align:center; margin-top:0; }
        .contenedor { max-width: 680px; margin: 0 auto; background:#fff; border:1px solid #d8a7b1; border-radius:10px; padding:20px; }

        label { display:block; margin-top:12px; font-weight:bold; }
        input[type="text"], textarea, select { width:100%; padding:10px; border:1px solid #d8a7b1; border-radius:6px; background:#fff; }
        textarea { min-height:110px; resize:vertical; }
        .help { color:#8b3f67; font-size:12px; margin-top:4px; }
        .errores { background:#fff1f5; border:1px solid #d8a7b1; color:#8b3f67; padding:10px; border-radius:8px; margin-bottom:10px; }

        .btn{ display:inline-flex; align-items:center; justify-content:center; gap:6px;
              background:#a64d79; color:#fff; text-decoration:none; padding:10px 15px;
              border-radius:6px; border:1px solid transparent; cursor:pointer; }
        .btn:hover{ background:#8b3f67; }

        .form-actions{ display:flex; gap:12px; align-items:center; justify-content:flex-start; margin-top:16px; }
        .form-actions button{ margin:0; }
        .form-actions .btn{ line-height:1; }
    </style>
</head>
<body>
<div class="contenedor">
    @php
        // isEdit si la ruta trae el parámetro {categoria} (funciona aunque cambie el nombre de la ruta)
        $routeParam = request()->route('categoria');      // null en /categoria/create; id o modelo en /categoria/{id}/edit
        $isEdit     = !is_null($routeParam);

        // Tomar valores seguros para los campos
        $nombreVal = old('nombre', $isEdit ? ($categoria->nombre ?? '') : '');
        $descVal   = old('descripcion', $isEdit ? ($categoria->descripcion ?? '') : '');
        $estadoVal = old('estado', $isEdit ? ($categoria->estado ?? 1) : 1);

        // ID para el action update (por si el binding entrega modelo o id)
        $categoriaId = $isEdit ? (is_object($routeParam) ? $routeParam->getKey() : $routeParam) : null;
    @endphp

    <h1>{{ $isEdit ? 'Editar Categoría' : 'Agregar Categoría' }}</h1>

    @if ($errors->any())
        <div class="errores">
            <strong>Revisa los campos:</strong>
            <ul style="margin:6px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('categoria.update', ['categoria' => $categoriaId]) : route('categoria.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <label for="nombre">Nombre</label>
        <input id="nombre" type="text" name="nombre" value="{{ $nombreVal }}" required>
        <div class="help">Ej.: “Tortas”, “Galletas”, “Helados”.</div>
        @error('nombre') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion">{{ $descVal }}</textarea>
        @error('descripcion') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="1" {{ (string)$estadoVal === '1' ? 'selected' : '' }}>Activa</option>
            <option value="0" {{ (string)$estadoVal === '0' ? 'selected' : '' }}>Inactiva</option>
        </select>
        @error('estado') <div class="help" style="color:#c00">{{ $message }}</div> @enderror

        <div class="form-actions">
            <button type="submit" class="btn">{{ $isEdit ? 'Actualizar Categoría' : 'Guardar Categoría' }}</button>
            <a href="{{ route('categoria.index') }}" class="btn">⬅️ Volver al listado</a>
        </div>
    </form>
</div>
</body>
</html>
