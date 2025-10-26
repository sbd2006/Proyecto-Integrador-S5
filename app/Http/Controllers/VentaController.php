<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;


class VentaController extends Controller
{
    public function index()
    {
        // Mostrar todas las ventas con usuario y detalles
        $ventas = Venta::with(['usuario', 'detalles.producto'])->latest()->paginate(10);
        return view('admin.venta.index', compact('ventas'));
    }

    public function show(Venta $venta)
    {
        $venta->load('detalles.producto', 'usuario');
        return view('admin.ventas.show', compact('venta'));
    }

    public function store(Request $request)
    {
        // Supongamos que el carrito viene del session o request
        $carrito = session('carrito', []);
        $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);

        // 1️⃣ Crear la venta
        $venta = Venta::create([
            'user_id' => auth()->id(),
            'total' => $total,
        ]);

        // 2️⃣ Crear el detalle de la venta
        foreach ($carrito as $item) {
            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio' => $item['precio'],
            ]);
        }

        // 3️⃣ Vaciar carrito o redirigir
        session()->forget('carrito');

        return redirect()->route('venta.index')->with('success', 'Venta registrada correctamente');
    }

    public function cambiarEstado(Request $request, Venta $venta)
    {
        $venta->estado = $request->estado;
        $venta->save();

        return back()->with('success', 'Estado de la venta actualizado.');
    }
}

