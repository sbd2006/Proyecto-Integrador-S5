@extends('admin.dashboard')

@section('titulomain', 'Gestión de Ventas')

@section('contenido')
<div class="venta-container">
    <h2 class="titulo-seccion">📋 Lista de Ventas</h2>

    @if (session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if ($ventas->count() > 0)
        <table class="tabla-ventas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->usuario->name ?? 'Cliente no asignado' }}</td>
                        <td>${{ number_format($venta->total, 2) }}</td>
                        <td>
                            <span class="estado {{ strtolower($venta->estado ?? 'pendiente') }}">
                                {{ ucfirst($venta->estado ?? 'Pendiente') }}
                            </span>
                        </td>
                        <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('venta.show', $venta) }}" class="btn-ver">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="paginacion">
            {{ $ventas->links('vendor.livewire.tailwind') }}
        </div>
    @else
        <p class="no-ventas">No hay ventas registradas.</p>
    @endif
</div>

<style>
    .venta-container {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .titulo-seccion {
        color: #a64d79;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
    }

    .tabla-ventas {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff9fb;
    }

    .tabla-ventas th, .tabla-ventas td {
        border-bottom: 1px solid #f0c6d2;
        padding: 10px 14px;
        text-align: left;
    }

    .tabla-ventas th {
        background-color: #d8a7b1;
        color: #4b1e2f;
        font-weight: bold;
    }

    .tabla-ventas tr:hover {
        background-color: #fde8ef;
    }

    .estado {
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: bold;
    }

    .estado.pagado { background: #c3f0ca; color: #256029; }
    .estado.pendiente { background: #f9e4a7; color: #664d03; }
    .estado.cancelado { background: #f8c6c6; color: #7a1c1c; }

    .btn-ver {
        text-decoration: none;
        background-color: #a64d79;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 14px;
        transition: 0.2s;
    }

    .btn-ver:hover {
        background-color: #8b3f67;
    }

    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        padding: 8px 14px;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .no-ventas {
        text-align: center;
        color: #555;
        font-style: italic;
    }

    .paginacion {
        margin-top: 16px;
        display: flex;
        justify-content: center;
    }
</style>
@endsection
