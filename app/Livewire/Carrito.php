<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Carrito extends Component
{

    public $productos = [];
    public $totalVenta;

    protected $listeners = ['productoAgregado' => 'actualizarCarrito'];

    public function mount()
    {
        $this->productos = Session::get('cart', []);
        $this->calcularTotalVenta();
    }

    public function incrementarCantidad($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['cantidad']++;
            Session::put('cart', $cart);
            $this->dispatch('productoAgregado'); // actualiza el contador en el icono
        }
    }

    public function disminuirCantidad($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['cantidad'] > 1) {
                $cart[$id]['cantidad']--;
            } else {
                unset($cart[$id]); // eliminar si llega a 0
            }

            Session::put('cart', $cart);
            $this->dispatch('productoAgregado'); // vuelve a emitir el evento para actualizar el contador
        }
    }

    public function eliminarProducto($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            $this->actualizarCarrito();
            $this->dispatch('productoAgregado'); // Actualiza el ícono del carrito si lo usas
        }
    }


    public function vaciarCarrito()
    {
        Session::forget('cart');
        $this->productos = [];
        //emitir evento producto agregado 
        $this->dispatch('productoAgregado');
    }


    public function actualizarCarrito()
    {
        $this->productos = Session::get('cart', []);
        $this->calcularTotalVenta();
    }

    public function calcularTotalVenta()
    {
        $this->totalVenta = 0;
        foreach ($this->productos as $item) {
            if (isset($item['producto']->precio) && isset($item['cantidad'])) {
                $this->totalVenta += $item['producto']->precio * $item['cantidad'];
            }
        }
    }

    public function render()
    {
        return view('livewire.carrito');
    }
}
