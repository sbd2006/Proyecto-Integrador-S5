<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>
    Resumen de ventas
    {{ data_get($filters,'from', data_get($filters,'desde','')) }}
    a
    {{ data_get($filters,'to', data_get($filters,'hasta','')) }}
  </title>
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
  <p class="muted small">
    Período:
    {{ data_get($filters,'from', data_get($filters,'desde','')) }}
    a
    {{ data_get($filters,'to', data_get($filters,'hasta','')) }}
    —
    Estado:
    {{ ucfirst(data_get($filters,'status', data_get($filters,'estado','pagado'))) }}
    @if(!empty($generado))
      — Generado: {{ \Carbon\Carbon::parse($generado)->format('Y-m-d H:i') }}
    @endif
  </p>

  {{-- KPIs --}}
  <div class="kpis">
    <div class="kpi">
      <strong>Ingresos</strong>
      <div>$ {{ number_format((float) data_get($kpis,'ingresos',0), 2, ',', '.') }}</div>
    </div>
    <div class="kpi">
      <strong>Órdenes</strong>
      <div>{{ number_format((int) data_get($kpis,'ordenes',0)) }}</div>
    </div>
    <div class="kpi">
      <strong>Ticket promedio</strong>
      <div>$ {{ number_format((float) data_get($kpis,'ticketPromedio',0), 2, ',', '.') }}</div>
    </div>
    <div class="kpi">
      <strong>Por estado</strong>
      @php
        $p  = (int) data_get($porEstado, 'pagado', 0);
        $pe = (int) data_get($porEstado, 'pendiente', 0);
        $c  = (int) data_get($porEstado, 'cancelado', 0);
      @endphp
      <div>✔︎ {{ $p }} &nbsp; | &nbsp; ⏳ {{ $pe }} &nbsp; | &nbsp; ✖︎ {{ $c }}</div>
    </div>
  </div>

  <h3>Por método de pago</h3>
  <table>
    <thead>
      <tr><th>Método</th><th class="right">Órdenes</th><th class="right">Subtotal</th></tr>
    </thead>
    <tbody>
      @forelse($porMetodo as $row)
        <tr>
          <td>{{ data_get($row,'metodo','—') }}</td>
          <td class="right">{{ number_format((int) data_get($row,'ordenes', data_get($row,'conteo',0))) }}</td>
          <td class="right">$ {{ number_format((float) data_get($row,'subtotal', data_get($row,'total',0)), 2, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="3" class="muted">Sin datos.</td></tr>
      @endforelse
    </tbody>
  </table>

  <h3>Ventas por día</h3>
  <table>
    <thead>
      <tr><th>Fecha</th><th class="right">Órdenes</th><th class="right">Monto</th></tr>
    </thead>
    <tbody>
      @forelse($porDia as $d)
        <tr>
          <td>{{ \Carbon\Carbon::parse(data_get($d,'fecha'))->format('d/m/Y') }}</td>
          <td class="right">{{ number_format((int) data_get($d,'ordenes',0)) }}</td>
          <td class="right">$ {{ number_format((float) data_get($d,'monto',0), 2, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="3" class="muted">Sin datos.</td></tr>
      @endforelse
    </tbody>
  </table>

  @if(!empty($topProductos) && count($topProductos) > 0)
    <h3>Top productos (ingresos)</h3>
    <table>
      <thead><tr><th>Producto</th><th class="right">Unidades</th><th class="right">Ingresos</th></tr></thead>
      <tbody>
        @forelse($topProductos as $r)
          <tr>
            <td>{{ data_get($r,'producto', data_get($r,'nombre','—')) }}</td>
            <td class="right">{{ number_format((int) data_get($r,'unidades',0)) }}</td>
            <td class="right">$ {{ number_format((float) data_get($r,'ingresos',0), 2, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="3" class="muted">Sin datos.</td></tr>
        @endforelse
      </tbody>
    </table>
  @endif
</body>
</html>
