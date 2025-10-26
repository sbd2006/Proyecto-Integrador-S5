@extends('admin.dashboard')

@section('content')
<div class="container">
    <h1>Lista de Ventas</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->user->name }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
