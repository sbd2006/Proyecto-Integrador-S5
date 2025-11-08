<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;
use Illuminate\Support\Facades\Session;

class CatalogoProductos extends Component
{
    use WithPagination;

    public $search = "";
    public $cantidades = [];

    protected $listeners = ['productoAgregado' => 'actualizarCantidades'];

    public function mount()
    {
        $this->actualizarCantidades();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function actualizarCantidades()
    {
        $this->cantidades = [];
        if (Session::has('cart')) {
            $cart = Session::get('cart');
            foreach ($cart as $id => $item) {
                $this->cantidades[$id] = $item['cantidad'];
            }
        }
    }

public function addToCart($productoId)
{
    $producto = Producto::find($productoId);

    if (!$producto) {
        session()->flash('error', 'El producto no existe.');
        return;
    }

    if ($producto->stock <= 0) {
        session()->flash('error', 'El producto "' . $producto->nombre . '" no tiene stock disponible.');
        return;
    }

    $cart = Session::get('cart', []);

    // Si ya está en el carrito
    if (isset($cart[$productoId])) {
        $nuevaCantidad = $cart[$productoId]['cantidad'] + 1;

        if ($nuevaCantidad > $producto->stock) {
            session()->flash('error', 'Solo quedan ' . $producto->stock . ' unidades de "' . $producto->nombre . '".');
            return;
        }

        $cart[$productoId]['cantidad'] = $nuevaCantidad;
    } else {
        $cart[$productoId] = [
            'producto' => $producto,
            'cantidad' => 1
        ];
    }

    Session::put('cart', $cart);
    $this->actualizarCantidades();
    $this->dispatch('productoAgregado');
    session()->flash('success', 'Producto agregado al carrito.');
}

public function incrementarCantidad($productoId)
{
    $cart = Session::get('cart', []);
    $producto = Producto::find($productoId);

    if (!$producto) {
        session()->flash('error', 'El producto no existe.');
        return;
    }

    if (isset($cart[$productoId])) {
        $nuevaCantidad = $cart[$productoId]['cantidad'] + 1;

        if ($nuevaCantidad > $producto->stock) {
            session()->flash('error', 'No hay suficiente stock para "' . $producto->nombre . '". Stock disponible: ' . $producto->stock);
            return;
        }

        $cart[$productoId]['cantidad'] = $nuevaCantidad;
        Session::put('cart', $cart);
        $this->actualizarCantidades();
        $this->dispatch('productoAgregado');
    }
}


    public function decrementarCantidad($productoId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productoId])) {
            if ($cart[$productoId]['cantidad'] > 1) {
                $cart[$productoId]['cantidad']--;
            } else {
                unset($cart[$productoId]);
            }
            Session::put('cart', $cart);
        }
        $this->actualizarCantidades();
        $this->dispatch('productoAgregado');
    }

    public function render()
    {
        $productos = Producto::where('stock', '>', 0)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(2);

        return view('livewire.catalogo-productos', compact('productos'));
    }
}
