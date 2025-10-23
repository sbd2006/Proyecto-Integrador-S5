@extends('user.user')

@section('title', 'Tienda - Productos 🍰')

@section('content')
<style>
    body {
        background-color: #fff5f7;
        font-family: 'Figtree', sans-serif;
    }

    h1 {
        color: #a64d79;
        text-align: center;
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .contenedor-productos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
    }

    .producto-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-align: center;
    }

    .producto-card:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .producto-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .producto-info {
        padding: 15px;
    }

    .producto-nombre {
        font-size: 1.1rem;
        font-weight: 600;
        color: #4b1e2f;
        margin-bottom: 5px;
    }

    .producto-precio {
        color: #d63384;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .producto-categoria {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 10px;
    }

    .btn-agregar {
        background-color: #a64d79;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-bottom: 15px;
    }

    .btn-agregar:hover {
        background-color: #8b3f67;
    }

    .buscador {
        text-align: center;
        margin-bottom: 20px;
    }

    .buscador input {
        padding: 8px 12px;
        border: 1px solid #d8a7b1;
        border-radius: 8px;
        width: 250px;
        outline: none;
    }

    .buscador button {
        background: #a64d79;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        margin-left: 8px;
        cursor: pointer;
    }

    .buscador button:hover {
        background: #8b3f67;
    }
</style>

<div class="py-8">
    <h1>🛍️ Productos disponibles</h1>

    @if(session('success'))
    <div style="text-align:center; margin-bottom:20px; background:#d4edda; color:#155724; padding:10px; border-radius:8px;">
        {{ session('success') }}
    </div>
    @endif

    <div class="buscador">
        <form method="GET" action="{{ route('productos.index') }}">
            <input type="text" name="buscar" placeholder="Buscar producto..." value="{{ request('buscar') }}">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <div class="contenedor-productos">
        @forelse($productos as $producto)
        <div class="producto-card">
            @if($producto->imagen)
            <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}">
            @else
            <img src="{{ asset('img/meren.jpg') }}" alt="Imagen por defecto">
            @endif

            <div class="producto-info">
                <p class="producto-nombre">{{ $producto->nombre }}</p>
                <p class="producto-precio">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                <p class="producto-categoria">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</p>

                {{-- Botón agregar al carrito --}}
                <form method="POST" action="{{ route('carrito.agregar', $producto->id) }}">
                    @csrf
                    <button type="submit" class="btn-agregar">
                        🛒 Agregar al carrito
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-600 col-span-full">No hay productos disponibles.</p>
        @endforelse
    </div>

    <div class="mt-6 text-center">
        {{ $productos->links() }}
    </div>
</div>
@endsection