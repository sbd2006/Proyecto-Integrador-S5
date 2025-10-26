<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReportRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;   

class ReportController extends Controller
{
    // Formulario de filtros
    public function ventasForm(Request $request)
    {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());
        $status = $request->input('status', 'todos');
        $payment_method_id = $request->input('payment_method_id');

        $metodos = PaymentMethod::orderBy('nombre')->get(['id','nombre']);

        return view('reportes.ventas', compact('desde','hasta','status','payment_method_id','metodos'));
    }

    // Genera PDF
    public function ventasPdf(SalesReportRequest $request)
    {
        $desde = Carbon::parse($request->input('desde'))->startOfDay();
        $hasta = Carbon::parse($request->input('hasta'))->endOfDay();
        $status = $request->input('status', 'todos');
        $payment_method_id = $request->input('payment_method_id');

        // Si filtramos por "pagado", usamos paid_at para el intervalo; si no, created_at
        $fechaColumna = $status === 'pagado' ? 'paid_at' : 'created_at';

        $query = Order::query()
            ->with('paymentMethod:id,nombre')
            ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('paid_at'))
            ->whereBetween($fechaColumna, [$desde, $hasta]);

        if ($status && $status !== 'todos') {
            $query->where('status', $status);
        }
        if ($payment_method_id) {
            $query->where('payment_method_id', $payment_method_id);
        }

        $orders = $query->orderBy($fechaColumna)->get();

        $totalGeneral = $orders->sum('total');

        $porMetodo = $orders->groupBy('payment_method_id')->map(function ($g) {
            $pm = optional($g->first()->paymentMethod);
            return [
                'metodo' => $pm?->nombre ?? 'N/D',
                'conteo' => $g->count(),
                'total'  => $g->sum('total'),
            ];
        })->values();

        $pdf = Pdf::loadView('pdf.sales-report', [
            'desde' => $desde,
            'hasta' => $hasta,
            'status' => $status,
            'orders' => $orders,
            'porMetodo' => $porMetodo,
            'totalGeneral' => $totalGeneral,
            'generado' => now(),
        ])->setPaper('letter', 'portrait');

        $fname = sprintf('reporte_ventas_%s_%s.pdf', $desde->format('Ymd'), $hasta->format('Ymd'));
        return $pdf->stream($fname); // o ->download($fname)
    }

     public function resumen(Request $request)
    {
        // Defaults: último mes y estado "pagado" (ingresos reales)
        $desde = Carbon::parse($request->input('desde', now()->startOfMonth()->toDateString()))->startOfDay();
        $hasta = Carbon::parse($request->input('hasta', now()->toDateString()))->endOfDay();
        $status = $request->input('status', 'pagado'); // por defecto "pagado"
        $payment_method_id = $request->input('payment_method_id');

        $fechaColumna = $status === 'pagado' ? 'paid_at' : 'created_at';

        // Base query con filtros
        $base = Order::query()
            ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('paid_at'))
            ->whereBetween($fechaColumna, [$desde, $hasta]);

        if ($status && $status !== 'todos') $base->where('status', $status);
        if ($payment_method_id) $base->where('payment_method_id', $payment_method_id);

        // Métricas
        $total = (clone $base)->sum('total');
        $ordenes = (clone $base)->count();
        $ticket = $ordenes > 0 ? round($total / $ordenes, 2) : 0;

        // Conteo por estado (mismo rango/columna de fecha)
        $conteoPorEstado = (clone $base)
            ->select('status', DB::raw('COUNT(*) as c'), DB::raw('SUM(total) as s'))
            ->groupBy('status')
            ->pluck('c', 'status'); // ['pagado'=>x,'pendiente'=>y,'cancelado'=>z]

        // Por método de pago
        $porMetodo = (clone $base)
            ->select('payment_method_id', DB::raw('COUNT(*) as conteo'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method_id')
            ->with('paymentMethod:id,nombre')
            ->get()
            ->map(fn($row) => [
                'metodo' => optional($row->paymentMethod)->nombre ?? 'N/D',
                'conteo' => (int)$row->conteo,
                'total'  => (float)$row->total,
            ]);

        // Serie por día
        $porDia = (clone $base)
            ->select(DB::raw("DATE($fechaColumna) as fecha"), DB::raw('COUNT(*) as ordenes'), DB::raw('SUM(total) as monto'))
            ->groupBy(DB::raw("DATE($fechaColumna)"))
            ->orderBy(DB::raw("DATE($fechaColumna)"))
            ->get();

        // Top productos (si existe order_items)
        $tieneDetalle = Schema::hasTable('order_items');
        $topProductos = collect();
        if ($tieneDetalle) {
            $col = "o.$fechaColumna";
            $topProductos = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->join('productos as p', 'oi.producto_id', '=', 'p.id')
                ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('o.paid_at'))
                ->whereBetween($col, [$desde, $hasta])
                ->when($status && $status !== 'todos', fn($q) => $q->where('o.status', $status))
                ->when($payment_method_id, fn($q) => $q->where('o.payment_method_id', $payment_method_id))
                ->select('p.nombre', DB::raw('SUM(oi.cantidad) as unidades'), DB::raw('SUM(oi.subtotal) as ingresos'))
                ->groupBy('p.id', 'p.nombre')
                ->orderByDesc('ingresos')
                ->limit(5)
                ->get();
        }

        $metodos = PaymentMethod::orderBy('nombre')->get(['id','nombre']);

        return view('reportes.resumen', [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'status' => $status,
            'payment_method_id' => $payment_method_id,
            'metodos' => $metodos,
            'total' => $total,
            'ordenes' => $ordenes,
            'ticket' => $ticket,
            'conteoPorEstado' => $conteoPorEstado,
            'porMetodo' => $porMetodo,
            'porDia' => $porDia,
            'topProductos' => $topProductos,
            'tieneDetalle' => $tieneDetalle,
        ]);
    }

    public function resumenPdf(Request $request)
{
    $desde = Carbon::parse($request->input('desde', now()->startOfMonth()->toDateString()))->startOfDay();
    $hasta = Carbon::parse($request->input('hasta', now()->toDateString()))->endOfDay();
    $status = $request->input('status', 'pagado');
    $payment_method_id = $request->input('payment_method_id');

    $fechaColumna = $status === 'pagado' ? 'paid_at' : 'created_at';

    $base = Order::query()
        ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('paid_at'))
        ->whereBetween($fechaColumna, [$desde, $hasta]);

    if ($status && $status !== 'todos') $base->where('status', $status);
    if ($payment_method_id) $base->where('payment_method_id', $payment_method_id);

    $total   = (clone $base)->sum('total');
    $ordenes = (clone $base)->count();
    $ticket  = $ordenes > 0 ? round($total / $ordenes, 2) : 0;

    $conteoPorEstado = (clone $base)
        ->select('status', DB::raw('COUNT(*) as c'), DB::raw('SUM(total) as s'))
        ->groupBy('status')
        ->pluck('c', 'status');

    $porMetodo = (clone $base)
        ->select('payment_method_id', DB::raw('COUNT(*) as conteo'), DB::raw('SUM(total) as total'))
        ->groupBy('payment_method_id')
        ->with('paymentMethod:id,nombre')
        ->get()
        ->map(fn($row) => [
            'metodo' => optional($row->paymentMethod)->nombre ?? 'N/D',
            'conteo' => (int)$row->conteo,
            'total'  => (float)$row->total,
        ]);

    $porDia = (clone $base)
        ->select(DB::raw("DATE($fechaColumna) as fecha"), DB::raw('COUNT(*) as ordenes'), DB::raw('SUM(total) as monto'))
        ->groupBy(DB::raw("DATE($fechaColumna)"))
        ->orderBy(DB::raw("DATE($fechaColumna)"))
        ->get();

    $tieneDetalle = Schema::hasTable('order_items');
    $topProductos = collect();
    if ($tieneDetalle) {
        $col = "o.$fechaColumna";
        $topProductos = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('productos as p', 'oi.producto_id', '=', 'p.id')
            ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('o.paid_at'))
            ->whereBetween($col, [$desde, $hasta])
            ->when($status && $status !== 'todos', fn($q) => $q->where('o.status', $status))
            ->when($payment_method_id, fn($q) => $q->where('o.payment_method_id', $payment_method_id))
            ->select('p.nombre', DB::raw('SUM(oi.cantidad) as unidades'), DB::raw('SUM(oi.subtotal) as ingresos'))
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('ingresos')
            ->limit(8)
            ->get();
    }

    $pdf = Pdf::loadView('pdf.sales-summary', [
        'desde' => $desde->toDateString(),
        'hasta' => $hasta->toDateString(),
        'status' => $status,
        'total' => $total,
        'ordenes' => $ordenes,
        'ticket' => $ticket,
        'conteoPorEstado' => $conteoPorEstado,
        'porMetodo' => $porMetodo,
        'porDia' => $porDia,
        'topProductos' => $topProductos,
        'tieneDetalle' => $tieneDetalle,
        'generado' => now(),
    ])->setPaper('letter', 'landscape'); // horizontal para que quepan las tablas

    $fname = sprintf('resumen_ventas_%s_%s.pdf', $desde->format('Ymd'), $hasta->format('Ymd'));
    return $pdf->stream($fname);
}


}
