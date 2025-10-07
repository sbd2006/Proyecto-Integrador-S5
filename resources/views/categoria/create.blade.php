<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }} - Postres María José</title>
    <style>
        body{font-family:Arial;background:#fff9fb;color:#4b1e2f;margin:20px}
        h1{color:#a64d79;text-align:center}
        .contenedor{max-width:650px;margin:0 auto;background:#fff;border:1px solid #f1c4d4;border-radius:10px;padding:20px}
        label{display:block;margin-top:10px;font-weight:bold}
        input[type="text"], textarea, select{width:100%;padding:8px;margin-top:5px;border:1px solid #d8a7b1;border-radius:5px;background:#fff}
        button{background:#a64d79;color:#fff;border:none;padding:10px 15px;border-radius:5px;cursor:pointer;margin-top:15px}
        button:hover{background:#8b3f67}
        a.btn-volver{display:inline-block;text-decoration:none;color:#fff;background:#a64d79;padding:8px 12px;border-radius:5px;margin-top:15px}
        a.btn-volver:hover{background:#8b3f67}
    </style>
</head>
<body>
<div class="contenedor">
    <h1>{{ isset($categoria) ? 'Editar Categoría' : 'Agregar Nueva Categoría' }}</h1>

    <form method="POST" action="{{ isset($categoria) ? route('categoria.update',$categoria) : route('categoria.store') }}">
        @csrf
        @if(isset($categoria)) @method('PUT') @endif

        <label>Nombre:</label>
        <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required>
        @error('nombre')<div style="color:#c00">{{ $message }}</div>@enderror

        <label>Descripción:</label>
        <textarea name="descripcion">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>

        <label>Estado:</label>
        <select name="estado">
            <option value="1" @selected(old('estado',$categoria->estado ?? 1)==1)>Activa</option>
            <option value="0" @selected(old('estado',$categoria->estado ?? 1)==0)>Inactiva</option>
        </select>

        <button type="submit">{{ isset($categoria) ? 'Actualizar Categoría' : 'Guardar Categoría' }}</button>
        <a href="{{ route('categoria.index') }}" class="btn-volver">⬅️ Volver al listado</a>
    </form>
</div>
</body>
</html>
