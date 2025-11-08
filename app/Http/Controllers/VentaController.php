<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Exception;

class VentaController extends Controller
{
    public function index()
    {
        // Mostrar todas las ventas con usuario y detalles
        $ventas = Venta::with(['usuario', 'detalles.producto'])->latest()->paginate(10);
        return view('admin.venta.index', compact('ventas'));
    }

    public function show($id)
    {
        $venta = Venta::with(['detalles.producto', 'usuario'])->find($id);

        if (!$venta) {
            abort(404, 'Venta no encontrada');
        }

        return view('admin.venta.show', compact('venta'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction(); // ✅ Transacción por seguridad

        try {
            // 1️⃣ Crear la venta
            $venta = Venta::create([
                'user_id' => auth()->id(),
                'total'   => $request->total,
                'estado'  => 'pagado',
                'fecha'   => now(),
            ]);

            // 2️⃣ Recorrer los productos del carrito / pedido
            foreach ($request->productos as $item) {
                // Ejemplo: $item = ['producto_id' => 5, 'cantidad' => 2, 'precio' => 10000]

                // Crear el detalle de la venta
                DetalleVenta::create([
                    'venta_id'    => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad'    => $item['cantidad'],
                    'precio'      => $item['precio'],
                    'subtotal'    => $item['cantidad'] * $item['precio'],
                ]);

                // 3️⃣ Restar stock del producto
                $producto = Producto::find($item['producto_id']);
                if ($producto) {
                    // Verificar que haya suficiente stock
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                    }

                    $producto->decrement('stock', $item['cantidad']);
                }
            }

            DB::commit();
            return redirect()->route('admin.ventas.index')
                ->with('success', 'Venta registrada y stock actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, Venta $venta)
    {
        $venta->estado = $request->estado;
        $venta->save();

        return back()->with('success', 'Estado de la venta actualizado.');
    }
    /**
     * UI de venta rápida (monto + método). No crea detalle.
     */
    public function createQuick()
    {
        $metodos = PaymentMethod::activos()->orderBy('nombre')->get(['id', 'nombre', 'slug']);
        return view('ventas.rapida', compact('metodos'));
    }

    /**
     * Registra venta rápida (solo cabecera).
     */
    public function storeQuick(Request $request)
    {
        $data = $request->validate([
            'total' => ['required', 'numeric', 'min:0.01'],
            'payment_method_id' => ['required', Rule::exists('payment_methods', 'id')],
            'email' => ['nullable', 'email'],
            'notas' => ['nullable', 'string', 'max:500'],
        ], [
            'total.min' => 'El total debe ser mayor a 0.',
        ]);

        $order = Order::create([
            'user_id' => auth()->id() ?? null,
            'total' => $data['total'],
            'payment_method_id' => $data['payment_method_id'],
            'status' => 'pagado',
            'referencia' => 'POS-' . Str::upper(Str::random(8)),
            'notas' => $data['notas'] ?? null,
            'paid_at' => now(),
        ]);

        // Opcional: enviar factura por correo si configuran MAIL_*
        // if (!empty($data['email'])) {
        //     $pdf = Pdf::loadView('pdf.invoice', ['order'=>$order, 'method'=>$order->paymentMethod]);
        //     \Mail::to($data['email'])->send(new \App\Mail\InvoiceMail($order, $pdf->output()));
        // }

        return redirect()
            ->route('ventas.rapida.create')
            ->with('ok', "Venta rápida registrada. Ref {$order->referencia}")
            ->with('factura_url', route('ventas.factura', $order));
    }

    /**
     * Finaliza una venta a partir de items enviados por el carrito (externo).
     * Espera: items = [{ producto_id, cantidad }...]
     * Opcional: permite precio_unitario override si lo quiere el carrito (si no, usa precio_venta).
     */
    public function finalizarDesdeCarrito(Request $request)
    {
        $data = $request->validate([
            'payment_method_id' => ['required', Rule::exists('payment_methods', 'id')],
            'notas' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', Rule::exists('productos', 'id')],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio_unitario' => ['nullable', 'numeric', 'min:0'], // opcional
        ]);

        try {
            $order = DB::transaction(function () use ($data) {
                $total = 0;
                $lineas = [];

                foreach ($data['items'] as $row) {
                    $p = Producto::lockForUpdate()->findOrFail($row['producto_id']);
                    $cant = (int) $row['cantidad'];

                    if ($cant > $p->stock) {
                        throw new Exception("Stock insuficiente para {$p->nombre}. Disponible: {$p->stock}.");
                    }

                    $precio = isset($row['precio_unitario']) ? (float)$row['precio_unitario'] : (float)$p->precio_venta;
                    $subtotal = $precio * $cant;

                    $total += $subtotal;

                    $lineas[] = [
                        'producto_id'     => $p->id,
                        'cantidad'        => $cant,
                        'precio_unitario' => $precio,
                        'subtotal'        => $subtotal,
                    ];

                    // Descontar stock
                    $p->decrement('stock', $cant);
                }

                $order = Order::create([
                    'user_id'           => auth()->id() ?? null,
                    'total'             => $total,
                    'payment_method_id' => $data['payment_method_id'],
                    'status'            => 'pagado',
                    'referencia'        => 'POS-' . Str::upper(Str::random(8)),
                    'notas'             => $data['notas'] ?? null,
                    'paid_at'           => now(),
                ]);

                foreach ($lineas as $l) {
                    $l['order_id'] = $order->id;
                    OrderItem::create($l);
                }

                return $order;
            });
        } catch (Exception $e) {
            // si el carrito llama vía AJAX/JSON
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'order_id' => $order->id,
                'referencia' => $order->referencia,
                'factura_url' => route('ventas.factura', $order),
            ]);
        }

        return redirect()
            ->route('ventas.rapida.create')
            ->with('ok', "Venta registrada. Ref {$order->referencia}")
            ->with('factura_url', route('ventas.factura', $order));
    }

    public function factura(Order $order)
    {
        $order->load(['paymentMethod', 'items.producto']);
        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'method' => $order->paymentMethod,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("factura_{$order->referencia}.pdf");
    }
}
