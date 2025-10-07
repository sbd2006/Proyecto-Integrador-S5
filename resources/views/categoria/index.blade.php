<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías - Postres María José</title>
    <style>
        body{font-family:Arial;background:#fff9fb;color:#4b1e2f;margin:20px}
        h1{color:#a64d79;text-align:center}
        .contenedor{max-width:1000px;margin:0 auto;background:#fff;border:1px solid #f1c4d4;border-radius:10px;padding:20px}
        .acciones,.filtros{text-align:center;margin-bottom:15px}
        .btn{display:inline-block;padding:6px 12px;background:#a64d79;color:#fff;border-radius:5px;text-decoration:none;margin:5px}
        .btn:hover{background:#8b3f67}
        .success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:20px}
        .filtros form{display:inline-block;padding:10px 20px;background:#f8e1e7;border-radius:8px}
        .filtros label{margin-right:10px}
        .filtros input,.filtros select{padding:5px;margin-right:10px}
        table{width:100%;border-collapse:collapse;margin-top:15px}
        th,td{border:1px solid #d8a7b1;padding:8px;text-align:center}
        th{background:#f1c4d4}
        .acciones-row a,.acciones-row form{display:inline-block}
        .acciones-row form{margin:0}
        .acciones-row button{background:#cc4b4b;color:#fff;border:none;padding:6px 10px;border-radius:5px;cursor:pointer}
        .acciones-row button:hover{background:#b13f3f}
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Categorías</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    {{-- FILTROS --}}
    <div class="filtros">
        <form method="GET" action="{{ route('categoria.index') }}">
            <label>Buscar:</label>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Nombre o descripción…">

            <label>Estado:</label>
            <select name="estado">
                <option value="">Todas</option>
                <option value="1" @selected($estado==='1')>Activas</option>
                <option value="0" @selected($estado==='0')>Inactivas</option>
            </select>

            <button type="submit">🔍 Filtrar</button>
            <a href="{{ route('categoria.index') }}" class="btn">🔄 Limpiar</a>
        </form>
    </div>

    {{-- ACCIONES --}}
    <div class="acciones">
        <a href="{{ route('categoria.create') }}" class="btn">➕ Agregar Categoría</a>
    </div>

    {{-- TABLA --}}
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categorias as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->nombre }}</td>
                <td>{{ $c->descripcion }}</td>
                <td>{{ $c->estado ? 'Activa' : 'Inactiva' }}</td>
                <td class="acciones-row">
                    <a class="btn" href="{{ route('categoria.edit',$c) }}">✏️ Editar</a>
                    <form class="eliminar" action="{{ route('categoria.destroy',$c) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar esta categoría?')">
                        @csrf @method('DELETE')
                        <button type="submit">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Sin resultados</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px">{{ $categorias->links() }}</div>
</div>
</body>
</html>
