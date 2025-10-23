<div>
<!--ESTA ES LA VISTA -->
    <div class="productosearch">
        <input type="text" wire:model.lazy="search" placeholder="Buscar producto..." class="search" id="searchInput">

        @if ($search)
        <span wire:click="$set('search', '')"
            onclick="document.getElementById('searchInput').value=''"
            class="clear-icon"
            style="cursor:pointer;">✕</span>
        @endif

    </div>
    <div class="productos-grid">
        @foreach($productos as $producto)
        <div class="producto" wire:key="producto-{{ $producto->id }}">
            <img src="{{ asset('img/'.$producto->imagen) }}" alt="{{ $producto->imagen }}">
            <h3>{{ $producto->nombre }}</h3>
            <p>Precio: ${{ $producto->precio_venta }}</p>

            <button wire:click="addToCart({{ $producto->id }})" class="btn-agregar-carrito">Comprar🛒</button>
        </div>
        @endforeach

        @if($productos->isEmpty())
        <p>No se encontraron productos.</p>
        @endif

    </div>
    <div class="mt-4">

        {{ $productos->links('vendor.livewire.tailwind', ['scrollTo' => '#productos']) }}

    </div>
</div>