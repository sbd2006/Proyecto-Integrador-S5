<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }} - Postres María José</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* Base */
        body { font-family: Arial, sans-serif; background:#fff9fb; color:#4b1e2f; margin:20px; }
        h1   { color:#a64d79; text-align:center; margin-top:0; }

        /* Card */
        .contenedor { max-width: 680px; margin: 0 auto; background:#fff; border:1px solid #d8a7b1; border-radius:10px; padding:20px; }

        /* Inputs */
        label { display:block; margin-top:12px; font-weight:bold; }
        input[type="text"], textarea, select {
            width:100%; padding:10px; border:1px solid #d8a7b1; border-radius:6px; background:#fff;
        }
        textarea { min-height: 110px; resize: vertical; }
        .help   { color:#8b3f67; font-size:12px; margin-top: 4px; }

        /* Errores */
        .errores { background:#fff1f5; border:1px solid #d8a7b1; color:#8b3f67; padding:10px; border-radius:8px; margin-bottom:10px; }

        /* Botones unificados */
        .btn{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background:#a64d79; color:#fff;
            text-decoration:none;
            padding:10px 15px;
            border-radius:6px;
            border:1px solid transparent;
            cursor:pointer;
        }
        .btn:hover{ background:#8b3f67; }

        /* Contenedor de acciones: MISMA FILA */
        .form-actions{
            display:flex;
            gap:12px;               /* misma distancia entre ambos */
            align-items:center;     /* misma altura */
            justify-content:flex-start;
            margin-top:16px;
        }

        /* Evita márgenes raros del botón submit */
        .form-actions button{ margin:0; }
        /* Asegura misma altura visual para <a> y <button> */
        .form-actions .btn{ line-height:1; }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>{{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }}</h1>

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

        <!-- *** AQUÍ VAN AMBOS BOTONES EN LA MISMA FILA *** -->
        <div class="form-actions">
            <button type="submit" class="btn">
                {{ isset($categoria) ? 'Actualizar Categoría' : 'Guardar Categoría' }}
            </button>

            <a href="{{ route('categoria.index') }}" class="btn">⬅️ Volver al listado</a>
        </div>
        <!-- *** NO DEJES NINGÚN OTRO BOTÓN "VOLVER" FUERA DE ESTE DIV *** -->
    </form>
</div>
</body>
</html>
