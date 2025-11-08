<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Support\Facades\Auth;

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
    $producto = \App\Models\Producto::find($id);

    if (!$producto) {
        session()->flash('error', 'El producto no existe.');
        return;
    }

    if (isset($cart[$id])) {
        $nuevaCantidad = $cart[$id]['cantidad'] + 1;

        if ($nuevaCantidad > $producto->stock) {
            session()->flash('error', 'No hay suficiente stock para "' . $producto->nombre . '". Solo quedan ' . $producto->stock . ' unidades.');
            return;
        }

        $cart[$id]['cantidad'] = $nuevaCantidad;
        Session::put('cart', $cart);
        $this->dispatch('productoAgregado');
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
            if (isset($item['producto']->precio_venta) && isset($item['cantidad'])) {
                $this->totalVenta += $item['producto']->precio_venta * $item['cantidad'];
            }
        }
    }

    public function finalizarCompra()
    {
        if (count($this->productos) === 0) {
            session()->flash('error', 'No hay productos en el carrito.');
            return;
        }

        // Crear el pedido principal
        $pedido = Pedido::create([
            'cliente_id' => Auth::id(), // si también usas cliente_id
            'total' => $this->totalVenta,
            'estado' => 'pendiente',
            'direccion_entrega' => 'Dirección de ejemplo',
            'metodo_pago' => 'efectivo',
            'nota' => 'Sin observaciones',
        ]);


        // Crear los detalles del pedido
        foreach ($this->productos as $item) {
            PedidoDetalle::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item['producto']->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['producto']->precio_venta,
                'subtotal' => $item['producto']->precio_venta * $item['cantidad'],

            ]);
        }

        // Vaciar el carrito
        $this->vaciarCarrito();

        // Mensaje de éxito
        session()->flash('success', 'Pedido realizado con éxito. ¡Gracias por tu compra!');
    }

    public function render()
    {
        return view('livewire.carrito');
    }
}
