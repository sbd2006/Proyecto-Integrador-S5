<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
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
        $pedidos = Pedido::with('detalles.producto','cliente')->latest()->get();
        return response()->json($pedidos);
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
        'pedido' => $pedido->load('detalles.producto','cliente'),
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

        return response()->json($pedido->load('detalles.producto','cliente'));
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