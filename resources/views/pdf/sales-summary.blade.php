<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resumen de ventas {{ $desde }} a {{ $hasta }}</title>
  <style>
    *{ box-sizing:border-box; }
    body{ font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#333; }
    h1{ font-size: 20px; margin: 0 0 2px; }
    h3{ margin: 12px 0 6px; }
    .muted{ color:#666; }
    .kpis{ display: table; width: 100%; margin-top: 8px; }
    .kpi{ display: table-cell; padding: 8px; border:1px solid #ddd; }
    .kpi strong{ display:block; font-size: 14px; margin-bottom:3px; }
    table{ width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td{ border:1px solid #ddd; padding:6px; text-align:left; vertical-align: top; }
    th{ background:#f6f2f4; }
    .right{ text-align:right; }
    .small{ font-size: 11px; }
  </style>
</head>
<body>
  <h1>Resumen de ventas</h1>
  <p class="muted small">Período: {{ $desde }} a {{ $hasta }} — Estado: {{ ucfirst($status) }} — Generado: {{ $generado->format('Y-m-d H:i') }}</p>

  {{-- KPIs --}}
  <div class="kpis">
    <div class="kpi">
      <strong>Ingresos</strong>
      <div>$ {{ number_format($total, 2, ',', '.') }}</div>
    </div>
    <div class="kpi">
      <strong>Órdenes</strong>
      <div>{{ number_format($ordenes) }}</div>
    </div>
    <div class="kpi">
      <strong>Ticket promedio</strong>
      <div>$ {{ number_format($ticket, 2, ',', '.') }}</div>
    </div>
    <div class="kpi">
      <strong>Por estado</strong>
      @php $p=(int)($conteoPorEstado['pagado']??0); $pe=(int)($conteoPorEstado['pendiente']??0); $c=(int)($conteoPorEstado['cancelado']??0); @endphp
      <div>✔︎ {{ $p }} &nbsp; | &nbsp; ⏳ {{ $pe }} &nbsp; | &nbsp; ✖︎ {{ $c }}</div>
    </div>
  </div>

  <h3>Por método de pago</h3>
  <table>
    <thead><tr><th>Método</th><th class="right">Órdenes</th><th class="right">Subtotal</th></tr></thead>
    <tbody>
      @forelse($porMetodo as $row)
        <tr>
          <td>{{ $row['metodo'] }}</td>
          <td class="right">{{ number_format($row['conteo']) }}</td>
          <td class="right">$ {{ number_format($row['total'], 2, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="3" class="muted">Sin datos.</td></tr>
      @endforelse
    </tbody>
  </table>

  <h3>Ventas por día</h3>
  <table>
    <thead><tr><th>Fecha</th><th class="right">Órdenes</th><th class="right">Monto</th></tr></thead>
    <tbody>
      @forelse($porDia as $d)
        <tr>
          <td>{{ \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') }}</td>
          <td class="right">{{ number_format($d->ordenes) }}</td>
          <td class="right">$ {{ number_format($d->monto, 2, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="3" class="muted">Sin datos.</td></tr>
      @endforelse
    </tbody>
  </table>

  @if($tieneDetalle)
    <h3>Top productos (ingresos)</h3>
    <table>
      <thead><tr><th>Producto</th><th class="right">Unidades</th><th class="right">Ingresos</th></tr></thead>
      <tbody>
        @forelse($topProductos as $r)
          <tr>
            <td>{{ $r->nombre }}</td>
            <td class="right">{{ number_format($r->unidades) }}</td>
            <td class="right">$ {{ number_format($r->ingresos, 2, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3" class="muted">Sin datos.</td></tr>
        @endforelse
      </tbody>
    </table>
  @endif
</body>
</html>
