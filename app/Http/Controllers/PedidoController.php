<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    // Vista admin (Blade)
    public function panelAdmin()
    {
        return view('admin.pedidos'); // crearemos esta vista más abajo
    }

    // JSON para admin (polling)
    public function indexJson()
    {
        $pedidos = Pedido::with('detalles.producto', 'cliente')->latest()->get();
        return response()->json($pedidos);
    }

    public function mostrarPago($id)
    {
        // Verificar que el pedido pertenece al usuario autenticado
        $pedido = Pedido::where('id', $id)
            ->firstOrFail();

        // Obtener todos los métodos de pago disponibles
        $metodos = PaymentMethod::all();

        // Enviar ambos a la vista
        return view('checkout', compact('pedido', 'metodos'));
    }


    public function pagar(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'payment_method_id' => 'required', // si lo usas en tu formulario
        ]);

        // Buscar el pedido
        $pedido = Pedido::with('detalles')->findOrFail($request->pedido_id);

        // ✅ Crear la venta a partir del pedido
        $venta = Venta::create([
            'user_id' => $pedido->user_id ?? auth()->id(),
            'total' => $pedido->total,
            'estado' => 'pagado', // puedes usar “completado”, “finalizado”, etc.
        ]);

        // ✅ Crear los detalles de la venta (copiando del pedido)
        foreach ($pedido->detalles as $detalle) {
            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $detalle->producto_id,
                'cantidad' => $detalle->cantidad,
                'precio' => $detalle->precio,
            ]);
        }

        // ✅ Cambiar el estado del pedido a “pagado”
        $pedido->estado = 'pagado';
        $pedido->save();

        return redirect()->route('cliente.pedidos')
            ->with('ok', 'Compra completada con éxito. Se generó la venta' . $venta->id);
    }

    public function verMetodoPago($id)
    {
        $pedido = Pedido::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('cliente.pago', compact('pedido'));
    }

    public function cancelar($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);

            // Verifica que el pedido esté pendiente
            if ($pedido->estado !== 'pendiente') {
                return response()->json(['success' => false, 'message' => 'El pedido no se puede cancelar.']);
            }

            $pedido->estado = 'cancelado';
            $pedido->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error al cancelar pedido: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno'], 500);
        }
    }



    // Cliente crea pedido
    public function store(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|integer|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'total' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'cliente_id' => Auth::id(),
                'total' => $request->total,
                'direccion_entrega' => $request->direccion_entrega,
                'metodo_pago' => $request->metodo_pago,
                'nota' => $request->nota,
            ]);

            foreach ($request->productos as $p) {
                $producto = Producto::findOrFail($p['producto_id']);
                $cantidad = $p['cantidad'];
                $precio = $producto->precio;
                $subtotal = $precio * $cantidad;

                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'pedido' => $pedido->load('detalles.producto', 'cliente'),
                'total_pedidos' => Pedido::where('cliente_id', Auth::id())->count()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'No se pudo crear el pedido', 'detalle' => $e->getMessage()], 500);
        }
    }

    // Obtener pedidos del cliente (JSON)
    public function pedidosPorCliente()
    {
        $pedidos = Pedido::with('detalles.producto')->where('cliente_id', Auth::id())->latest()->get();
        return response()->json($pedidos);
    }

    // Actualizar estado (admin)
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_preparacion,listo,entregado,cancelado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json($pedido->load('detalles.producto', 'cliente'));
    }

    public function contarPedidosCliente()
    {
        $cantidad = Pedido::where('cliente_id', Auth::id())->whereIn('estado', ['pendiente', 'en_preparacion'])->count();

        return response()->json(['cantidad' => $cantidad]);
    }


    public function vistaPedidosCliente()
    {
        return view('cliente.pedidos');
    }
}
