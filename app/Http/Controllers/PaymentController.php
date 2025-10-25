<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PaymentController extends Controller
{
    public function checkout()
    {
        $metodos = PaymentMethod::activos()->orderBy('nombre')->get();
        return view('checkout', compact('metodos'));
    }

    public function pagar(Request $r)
    {
        $r->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'total'             => 'required|numeric|min:0',
            'notas'             => 'nullable|string|max:255',
            // si no hay login, el email es opcional (solo para mostrarlo en la factura)
            'email'             => Auth::check() ? 'nullable|email' : 'nullable|email',
        ]);

        $order = Order::create([
            'user_id'           => Auth::id(), // o null si no hay login
            'total'             => $r->input('total'),
            'payment_method_id' => $r->input('payment_method_id'),
            'status'            => 'pendiente',
            'referencia'        => 'SIM-'.Str::upper(Str::random(8)),
            'notas'             => $r->input('notas'),
        ]);

        $method = $order->paymentMethod;

        // Generar PDF y abrirlo inline en el navegador
        $pdf = PDF::loadView('pdf.invoice', [
            'order'  => $order,
            'method' => $method,
            'email'  => Auth::user()->email ?? $r->input('email'),
        ]);

        return $pdf->stream("Factura_{$order->referencia}.pdf");
    }
}
