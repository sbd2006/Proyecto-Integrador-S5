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
    // -------------------------------
    //  Reporte "ventas" (form y PDF)
    // -------------------------------

    // Formulario de filtros (reporte detallado de ventas)
    public function ventasForm(Request $request)
    {
        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());
        $status = $request->input('status', 'todos');
        $payment_method_id = $request->input('payment_method_id');

        $metodos = PaymentMethod::orderBy('nombre')->get(['id','nombre']);

        return view('reportes.ventas', compact('desde','hasta','status','payment_method_id','metodos'));
    }

    // Genera PDF del reporte "ventas" (no el resumen)
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

    // -------------------------------
    //  RESUMEN DE VENTAS (HTML + PDF)
    // -------------------------------

    // Página HTML del resumen
    public function resumen(Request $request)
    {
        [$desde, $hasta, $status, $payment_method_id, $fechaColumna] = $this->normalizeFilters($request);
        $base = $this->buildBaseQuery($fechaColumna, $desde, $hasta, $status, $payment_method_id);

        // Cálculo único de métricas y series
        $raw = $this->buildSummaryRaw($base, $fechaColumna, $desde, $hasta, $status, $payment_method_id);
        $vm  = $this->buildViewModel($raw, $desde, $hasta, $status, $payment_method_id);

        // Catálogo de métodos para los selectores de la vista
        $vm['metodos'] = PaymentMethod::orderBy('nombre')->get(['id','nombre']);

        return view('reportes.resumen', $vm);
    }

    // PDF del resumen
    public function resumenPdf(Request $request)
    {
        [$desde, $hasta, $status, $payment_method_id, $fechaColumna] = $this->normalizeFilters($request);
        $base = $this->buildBaseQuery($fechaColumna, $desde, $hasta, $status, $payment_method_id);

        $raw = $this->buildSummaryRaw($base, $fechaColumna, $desde, $hasta, $status, $payment_method_id);
        $vm  = $this->buildViewModel($raw, $desde, $hasta, $status, $payment_method_id);
        $vm['generado'] = now();

        $pdf = Pdf::loadView('pdf.sales-summary', $vm)->setPaper('letter', 'landscape');
        $fname = sprintf('resumen_ventas_%s_%s.pdf', $desde->format('Ymd'), $hasta->format('Ymd'));

        return $pdf->stream($fname);
    }

    // -----------------
    //  Helper methods
    // -----------------

    /**
     * Normaliza filtros con defaults:
     * - rango por defecto: mes actual
     * - estado por defecto: TODOS (usa created_at)
     * - payment_method_id: opcional
     * Devuelve: [Carbon $desde, Carbon $hasta, string $status, ?int $payment_method_id, string $fechaColumna]
     */
    private function normalizeFilters(Request $request): array
    {
        $desde = Carbon::parse($request->input('desde', now()->startOfMonth()->toDateString()))->startOfDay();
        $hasta = Carbon::parse($request->input('hasta', now()->toDateString()))->endOfDay();
        $status = $request->input('status', 'todos'); // <-- cambiado: default "todos"
        $payment_method_id = $request->input('payment_method_id');

        // Con "todos" usamos created_at; solo "pagado" usa paid_at
        $fechaColumna = $status === 'pagado' ? 'paid_at' : 'created_at';

        return [$desde, $hasta, $status, $payment_method_id, $fechaColumna];
    }

    /**
     * Construye el query base de Orders aplicando filtros comunes.
     */
    private function buildBaseQuery(string $fechaColumna, Carbon $desde, Carbon $hasta, string $status, $payment_method_id)
    {
        $base = Order::query()
            ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('paid_at'))
            ->whereBetween($fechaColumna, [$desde, $hasta]);

        if ($status && $status !== 'todos') {
            $base->where('status', $status);
        }
        if ($payment_method_id) {
            $base->where('payment_method_id', $payment_method_id);
        }
        return $base;
    }

    /**
     * Calcula métricas y colecciones del resumen a partir del query base.
     * Devuelve un arreglo "crudo" con claves: ingresos, ordenes, ticketPromedio, porEstado, porMetodo, porDia, topProductos
     */
    private function buildSummaryRaw($base, string $fechaColumna, Carbon $desde, Carbon $hasta, string $status, $payment_method_id): array
    {
        // KPIs
        $ingresos = (float) (clone $base)->sum('total');
        $ordenes  = (int)   (clone $base)->count();
        $ticket   = $ordenes > 0 ? round($ingresos / max($ordenes,1), 2) : 0.0;

        // Conteo por estado (completa claves por si no aparecen en el rango)
        $conteoEstados = (clone $base)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $porEstado = [
            'pagado'    => (int) ($conteoEstados['pagado']    ?? 0),
            'pendiente' => (int) ($conteoEstados['pendiente'] ?? 0),
            'cancelado' => (int) ($conteoEstados['cancelado'] ?? 0),
        ];

        // Por método de pago
        $porMetodo = DB::table('orders as o')
            ->leftJoin('payment_methods as pm', 'o.payment_method_id', '=', 'pm.id')
            ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('o.paid_at'))
            ->whereBetween("o.$fechaColumna", [$desde, $hasta])
            ->when($status && $status !== 'todos', fn($q) => $q->where('o.status', $status))
            ->when($payment_method_id, fn($q) => $q->where('o.payment_method_id', $payment_method_id))
            ->selectRaw("COALESCE(pm.nombre, 'N/D') as metodo, COUNT(*) as ordenes, SUM(o.total) as subtotal")
            ->groupBy('pm.id', 'pm.nombre')
            ->orderByDesc('subtotal')
            ->get();

        // Serie por día
        $porDia = (clone $base)
            ->select(DB::raw("DATE($fechaColumna) as fecha"), DB::raw('COUNT(*) as ordenes'), DB::raw('SUM(total) as monto'))
            ->groupBy(DB::raw("DATE($fechaColumna)"))
            ->orderBy(DB::raw("DATE($fechaColumna)"))
            ->get();

        // Top productos (si existe order_items)
        $topProductos = collect();
        if (Schema::hasTable('order_items')) {
            $col = "o.$fechaColumna";
            $topProductos = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->join('productos as p', 'oi.producto_id', '=', 'p.id')
                ->when($fechaColumna === 'paid_at', fn($q) => $q->whereNotNull('o.paid_at'))
                ->whereBetween($col, [$desde, $hasta])
                ->when($status && $status !== 'todos', fn($q) => $q->where('o.status', $status))
                ->when($payment_method_id, fn($q) => $q->where('o.payment_method_id', $payment_method_id))
                ->select('p.nombre as producto', DB::raw('SUM(oi.cantidad) as unidades'), DB::raw('SUM(oi.subtotal) as ingresos'))
                ->groupBy('p.id', 'p.nombre')
                ->orderByDesc('ingresos')
                ->limit(8)
                ->get();
        }

        return [
            'ingresos'      => $ingresos,
            'ordenes'       => $ordenes,
            'ticketPromedio'=> $ticket,
            'porEstado'     => $porEstado,
            'porMetodo'     => $porMetodo,
            'porDia'        => $porDia,
            'topProductos'  => $topProductos,
        ];
    }

    /**
     * Adapta los datos crudos a un View Model estable para las vistas (HTML/PDF).
     */
    private function buildViewModel(array $raw, Carbon $desde, Carbon $hasta, string $status, $payment_method_id): array
    {
        return [
            'filters' => [
                'from'   => $desde->toDateString(),
                'to'     => $hasta->toDateString(),
                'estado' => $status,
                'metodo' => $payment_method_id ?: 'todos',
                // alias legacy
                'desde'  => $desde->toDateString(),
                'hasta'  => $hasta->toDateString(),
                'status' => $status,
                'payment_method_id' => $payment_method_id,
            ],
            'kpis' => [
                'ingresos'       => (float) ($raw['ingresos'] ?? 0),
                'ordenes'        => (int)   ($raw['ordenes'] ?? 0),
                'ticketPromedio' => (float) ($raw['ticketPromedio'] ?? 0),
            ],
            'porEstado'    => $raw['porEstado'] ?? [],
            'porMetodo'    => $raw['porMetodo'] ?? collect(),
            'porDia'       => $raw['porDia'] ?? collect(),
            'topProductos' => $raw['topProductos'] ?? collect(),
        ];
    }
}
