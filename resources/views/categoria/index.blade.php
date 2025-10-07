<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías - Postres María José</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff9fb; color: #4b1e2f; margin: 20px; }
        h1 { color: #a64d79; text-align: center; }

        .contenedor { max-width: 1000px; margin: 0 auto; background: #fff; border: 1px solid #d8a7b1; border-radius: 10px; padding: 20px; }

        .topbar { display: flex; gap: 10px; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 12px; }
        .btn { display: inline-block; background: #a64d79; color: #fff; text-decoration: none; padding: 8px 12px; border-radius: 5px; }
        .btn:hover { background: #8b3f67; }

        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }

        .filtros { background: #f8e1e7; padding: 12px; border-radius: 8px; margin-bottom: 15px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .filtros input[type="text"], .filtros select { padding: 8px; border: 1px solid #d8a7b1; border-radius: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d8a7b1; padding: 8px; text-align: center; }
        th { background-color: #f8e1e7; }
        tr:nth-child(even) { background-color: #fff1f5; }

        .acciones-row { display: flex; gap: 8px; justify-content: center; align-items: center; }
        .acciones-row button { background: #a64d79; color: #fff; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .acciones-row button:hover { background: #8b3f67; }

        .paginacion { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="topbar">
        <h1 style="margin:0">Categorías</h1>
        <a href="{{ route('categoria.create') }}" class="btn">+ Nueva categoría</a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('categoria.index') }}" class="filtros">
        <input type="text" name="buscar" value="{{ $buscar ?? '' }}" placeholder="Buscar por nombre o descripción…">
        @php $estadoSel = (string)($estado ?? ''); @endphp
        <select name="estado">
            <option value=""  {{ $estadoSel === '' ? 'selected' : '' }}>Todas</option>
            <option value="1" {{ $estadoSel === '1' ? 'selected' : '' }}>Activas</option>
            <option value="0" {{ $estadoSel === '0' ? 'selected' : '' }}>Inactivas</option>
        </select>
        <button type="submit" class="btn">Filtrar</button>
        <a href="{{ route('categoria.index') }}" class="btn">Limpiar</a>
    </form>

    @if ($categorias->count() === 0)
        <p style="text-align:center; color:#8b3f67; margin: 14px 0;">No hay categorías que coincidan con el filtro.</p>
    @else
        <table>
            <thead>
            <tr>
                <th style="width:80px">ID</th>
                <th style="width:220px">Nombre</th>
                <th>Descripción</th>
                <th style="width:120px">Estado</th>
                <th style="width:220px">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($categorias as $c)
                <tr>
                    <td>#{{ $c->id }}</td>
                    <td>{{ $c->nombre }}</td>
                    <td>{{ $c->descripcion }}</td>
                    <td>
                        @if((string)$c->estado === '1')
                            <span>Activo</span>
                        @else
                            <span>Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="acciones-row">
                            <a class="btn" href="{{ route('categoria.edit', ['categoria' => $c]) }}">✏️ Editar</a>

                            <form action="{{ route('categoria.destroy', ['categoria' => $c]) }}" method="POST" class="form-eliminar" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit">🗑️ Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="paginacion">
            {{ $categorias->withQueryString()->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.form-eliminar');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const overlay = document.createElement('div');
            overlay.innerHTML = `
                <div style="
                    position: fixed; inset: 0;
                    background: rgba(0,0,0,0.5);
                    display: flex; justify-content: center; align-items: center; z-index: 1000;">
                    <div style="
                        background: #fff; padding: 20px; border-radius: 10px; width: 320px; text-align: center;
                        color: #4b1e2f; font-family: Arial;">
                        <p style="margin-bottom: 14px;">¿Seguro que deseas eliminar esta categoría?</p>
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button id="btnSi" style="background:#a64d79; color:#fff; border:none; padding:8px 15px; border-radius:5px;">Sí, eliminar</button>
                            <button id="btnNo" style="background:#ccc; color:#111; border:none; padding:8px 15px; border-radius:5px;">Cancelar</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            overlay.querySelector('#btnSi').onclick = () => form.submit();
            overlay.querySelector('#btnNo').onclick = () => overlay.remove();
        });
    });
});
</script>
</body>
</html>
