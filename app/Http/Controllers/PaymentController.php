<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pedido;
use Exception;

class PaymentController extends Controller
{
    public function checkout()
    {
        $metodos = PaymentMethod::activos()->orderBy('nombre')->get();

        $pedido = Pedido::where('user_id', auth()->id())
            ->where('estado', 'pendiente')
            ->first();

        return view('checkout', compact('metodos', 'pedido'));
    }

    public function pagar(Request $request)
    {
        $validated = $request->validate([
            'pedido_id'         => 'required|exists:pedidos,id',
            'total'             => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notas'             => 'nullable|string|max:255',
            'email'             => 'nullable|email',
        ]);

        $pedido = Pedido::with('detalles.producto')->findOrFail($validated['pedido_id']);

        try {

            // ✅ Toda la operación dentro de una transacción
            $order = DB::transaction(function () use ($pedido, $validated, $request) {

                // ✅ 1. Crear la venta
                $venta = Venta::create([
                    'user_id' => auth()->id(),
                    'total'   => $validated['total'],
                    'estado'  => 'pagado',
                ]);

                // ✅ 2. Actualizar stock y registrar detalles de venta
                foreach ($pedido->detalles as $detalle) {

                    $producto = \App\Models\Producto::lockForUpdate()
                        ->find($detalle->producto_id);

                    if (!$producto) {
                        throw new Exception("El producto con ID {$detalle->producto_id} no existe.");
                    }

                    $producto->decrement('stock', $detalle->cantidad);

                    DetalleVenta::create([
                        'venta_id'    => $venta->id,
                        'producto_id' => $detalle->producto_id,
                        'cantidad'    => $detalle->cantidad,
                        'precio'      => $detalle->precio_unitario,
                    ]);
                }

                // ✅ 3. Cambiar estado del pedido
                $pedido->update(['estado' => 'pagado']);

                // ✅ 4. Crear la orden para la factura
                $order = Order::create([
                    'user_id'           => Auth::id(),
                    'total'             => $validated['total'],
                    'payment_method_id' => $validated['payment_method_id'],
                    'status'            => 'pagado',
                    'referencia'        => 'ORD-' . strtoupper(Str::random(8)),
                    'notas'             => $validated['notas'] ?? null,
                ]);

                // ✅ 5. Registrar los ítems de la orden
                foreach ($pedido->detalles as $detalle) {
                    $order->items()->create([
                        'producto_id'     => $detalle->producto_id,
                        'cantidad'        => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal'        => $detalle->subtotal,
                    ]);
                }

                return $order;
            });

            // ✅ 6. Generar PDF (fuera de la transacción)
            $pdf = PDF::loadView('pdf.invoice', [
                'order'  => $order->load('items.producto'),
                'method' => $order->paymentMethod,
                'email'  => Auth::user()->email ?? $request->input('email'),
            ]);

            return $pdf->stream("Factura_{$order->referencia}.pdf");

        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
