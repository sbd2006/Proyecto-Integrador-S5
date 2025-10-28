@extends('admin.dashboard')

{{-- Título centrado --}}
@section('titulomain')
  <div style="text-align:center;">
    {{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }}
  </div>
@endsection

@section('contenido')
<style>
  .contenedor {
      max-width: 650px; margin: 0 auto; background:#fff;
      border:1px solid #d8a7b1; border-radius:10px; padding:20px;
      box-shadow:0 8px 20px rgba(31,41,55,.06);
  }
  label { display:block; margin-top:10px; font-weight:bold; color:#6b4255; }
  input[type="text"], textarea, select {
      width:100%; padding:10px; border:1px solid #d8a7b1; border-radius:5px; background:#fff;
  }
  textarea { min-height:110px; resize:vertical; }
  .errores {
      background:#fff1f5; border:1px solid #d8a7b1; color:#8b3f67;
      padding:10px; border-radius:8px; margin-bottom:10px;
  }
  .help { color:#8b3f67; font-size:12px; margin-top:4px; }

  /* Botonera */
  .actions { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:16px; }
  .btn {
      border:1px solid #d8a7b1; border-radius:8px; padding:10px 14px; font-weight:800;
      font-size:14px; cursor:pointer; background:#fff; color:#7b3a58; text-decoration:none;
      display:inline-block; text-align:center;
  }
  .btn:hover { filter:brightness(.98); }
  .btn-primary { background:#a64d79; color:#fff; border-color:transparent; }
  .btn-primary:hover { background:#8b3f67; }
  .btn-wide { min-width:220px; } /* mismo ancho */
</style>

@php
  $catIndexRoute = Route::has('categoria.index') ? 'categoria.index'
                  : (Route::has('categorias.index') ? 'categorias.index'
                  : (Route::has('category.index') ? 'category.index' : null));

  $prodIndexRoute = Route::has('producto.index') ? 'producto.index'
                  : (Route::has('productos.index') ? 'productos.index' : null);
@endphp

<div class="contenedor">
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

      <div class="actions">
          <button type="submit" class="btn btn-primary btn-wide">
              {{ isset($categoria) ? 'Actualizar Categoría' : 'Guardar Categoría' }}
          </button>

          @if($catIndexRoute)
            {{-- 🔄 Mismo color y nuevo texto --}}
            <a href="{{ route($catIndexRoute) }}" class="btn btn-primary btn-wide">⬅️ Ir al listado</a>
          @endif

          @if($prodIndexRoute)
            {{-- 🔄 Mismo color que el botón principal --}}
            <a href="{{ route($prodIndexRoute) }}" class="btn btn-primary btn-wide">↩️ Volver a mis Productos</a>
          @endif
      </div>
  </form>
</div>
@endsection
  