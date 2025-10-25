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

        $cart = Session::get('cart', []);
        if (isset($cart[$productoId])) {
            $cart[$productoId]['cantidad']++;
        } else {
            $cart[$productoId] = [
                'producto' => $producto,
                'cantidad' => 1
            ];
        }

        Session::put('cart', $cart);
        $this->actualizarCantidades();
        $this->dispatch('productoAgregado');
    }

    public function incrementarCantidad($productoId)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$productoId])) {
            $cart[$productoId]['cantidad']++;
            Session::put('cart', $cart);
        }
        $this->actualizarCantidades();
        $this->dispatch('productoAgregado');
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
