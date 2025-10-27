@extends('admin.dashboard')

@section('titulomain', 'Detalle de Venta')

@section('contenido')
<div class="venta-detalle">
    <h2>🧾 Detalle de la Venta #{{ $venta->id }}</h2>

    <p><strong>Cliente:</strong> {{ $venta->usuario->name ?? 'No asignado' }}</p>
    <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</p>
    <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>

    <h3>Productos</h3>
    @if($venta->detalles->count() > 0)
        <table class="tabla-detalle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio, 2) }}</td>
                        <td>${{ number_format($detalle->cantidad * $detalle->precio, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay productos en esta venta.</p>
    @endif

    <div class="acciones">
        <a href="{{ route('venta.index') }}" class="btn-volver">⬅ Volver</a>
    </div>
</div>

<style>
    .venta-detalle {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
        color: #a64d79;
        margin-bottom: 10px;
    }
    .tabla-detalle {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .tabla-detalle th, .tabla-detalle td {
        border: 1px solid #f0c6d2;
        padding: 8px;
        text-align: left;
    }
    .btn-volver {
        display: inline-block;
        margin-top: 15px;
        background: #a64d79;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }
    .btn-volver:hover {
        background: #8b3f67;
    }
</style>
@endsection
