<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pedido;

class PaymentController extends Controller
{
    public function checkout()
    {
        // Obtener los métodos de pago activos
        $metodos = PaymentMethod::activos()->orderBy('nombre')->get();

        // Obtener el pedido pendiente del usuario
        $pedido = Pedido::where('user_id', auth()->id())
            ->where('estado', 'pendiente')
            ->first();

        // Enviar las variables a la vista
        return view('checkout', compact('metodos', 'pedido'));
    }


    public function pagar(Request $request)
    {
        // ✅ Validar datos
        $validated = $request->validate([
            'pedido_id'         => 'required|exists:pedidos,id',
            'total'             => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'notas'             => 'nullable|string|max:255',
            'email'             => Auth::check() ? 'nullable|email' : 'nullable|email',
        ]);

        // ✅ Buscar el pedido con sus detalles
        $pedido = Pedido::with('detalles.producto')->findOrFail($validated['pedido_id']);

        // ✅ Crear la venta
        $venta = Venta::create([
            'user_id' => auth()->id(),
            'total'   => $validated['total'],
            'estado'  => 'pagado',
        ]);

        // ✅ Crear los detalles de la venta
        foreach ($pedido->detalles as $detalle) {
            DetalleVenta::create([
                'venta_id'    => $venta->id,
                'producto_id' => $detalle->producto_id,
                'cantidad'    => $detalle->cantidad,
                'precio'      => $detalle->precio_unitario,
            ]);
        }

        // ✅ Cambiar estado del pedido
        $pedido->update(['estado' => 'pagado']);

        // ✅ Crear la orden (para generar factura)
        $order = Order::create([
            'user_id'           => Auth::id(),
            'total'             => $validated['total'],
            'payment_method_id' => $validated['payment_method_id'],
            'status'            => 'pagado',
            'referencia'        => 'ORD-' . strtoupper(Str::random(8)),
            'notas'             => $validated['notas'] ?? null,
        ]);

        // ✅ Agregar productos desde el pedido
        foreach ($pedido->detalles as $detalle) {
            $order->items()->create([
                'producto_id'     => $detalle->producto_id,
                'cantidad'        => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal'        => $detalle->subtotal,
            ]);
        }

        // ✅ Generar y mostrar la factura en PDF
        $pdf = PDF::loadView('pdf.invoice', [
            'order'  => $order->load('items.producto'),
            'method' => $order->paymentMethod,
            'email'  => Auth::user()->email ?? $request->input('email'),
        ]);

        return $pdf->stream("Factura_{$order->referencia}.pdf");
    }
}
