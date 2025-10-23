<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class IconoCarrito extends Component
{
    public $mostrarCarrito = false;
    public $cartCount = 0;

    //escuchar el evento agregar producto
    protected $listeners = ['productoAgregado' => 'actualizarContador'];

    public function actualizarContador()
    {
        $this->cartCount = $this->getCartCount();
    }

    public function getCartCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        if (Session::has('cart')) {
            $cart = Session::get('cart');
            return array_sum(array_column($cart, 'cantidad'));
        }

        return 0;
    }


    public function toggleCarrito()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->mostrarCarrito = !$this->mostrarCarrito;
    }


    public function mount()
    {
        $this->cartCount = $this->getCartCount();
    }

    public function render()
    {
        return view('livewire.icono-carrito');
    }
}
