<div>
    <div class="relative">
        <button wire:click="toggleCarrito" class="icono-carrito">
            🛒<span class="badge">{{$cartCount}}</span>
        </button>
        @if($mostrarCarrito)
            @livewire('carrito')
        @endif
    </div>
</div>