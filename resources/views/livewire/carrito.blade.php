<div class="carrito-flotante">
    <h3>Carrito</h3>

    @forelse($productos as $item)
    <div class="producto-carrito">
        <img src="{{ asset('img/' . $item['producto']->imagen) }}" alt="{{ $item['producto']->nombre }}">

        <div class="producto-info">
            <p><strong>{{ $item['producto']->nombre }}</strong></p>

            <!-- Botones + y - -->
            <div class="cantidad-control" style="display: flex; align-items: center; gap: 8px;">
                <button wire:click="disminuirCantidad({{ $item['producto']->id }})" class="btn-cantidad">−</button>

                <span>{{ $item['cantidad'] }}</span>

                <button wire:click="incrementarCantidad({{ $item['producto']->id }})" class="btn-cantidad">+</button>
            </div>

            <p>Subtotal:</p>
            <p>${{ number_format($item['producto']->precio * $item['cantidad'], 2) }}</p>
        </div>
        <button
            wire:click="eliminarProducto({{ $item['producto']->id }})" class="btn-quitarC">
            ✕
        </button>
    </div>
    @empty
    <center><p>No hay productos en el carrito</p></center>
    @endforelse

    @if(count($productos) > 0)
    <p class="total">Total: ${{ number_format($totalVenta, 2) }}</p>

    <button wire:click="vaciarCarrito" class="btn-vaciar">Vaciar carrito</button>
    <a href="" class="btn-finalizar">Finalizar Compra</a>
    @endif
</div>