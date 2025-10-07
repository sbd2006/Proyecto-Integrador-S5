<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function pdfProductos(Request $request)
    {
        $query = Producto::select('id','nombre','descripcion','precio','precio_venta','stock','categoria_id');

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        // Filtro por stock
        if ($request->stock == 'con') {
            $query->where('stock', '>', 0);
        } elseif ($request->stock == 'sin') {
            $query->where('stock', '<=', 0);
        }

        // Filtro por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $productos = $query->orderBy('id', 'ASC')->get();

        // Generar el PDF usando la vista
        $pdf = Pdf::loadView('pdf.productos', ['productos' => $productos]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('productos.pdf');
    }
}
