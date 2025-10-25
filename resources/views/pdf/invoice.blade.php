<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura {{ $order->referencia }}</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#333; }
    h1 { font-size: 18px; margin: 0 0 2px; }
    h3 { margin: 16px 0 6px; }
    .muted { color:#777; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border:1px solid #ccc; padding:8px; text-align:left; vertical-align: top; }
    th { background:#f7f7f7; }
    .right { text-align: right; }
    .totals { margin-top: 12px; width: 100%; border-collapse: collapse; }
    .totals th, .totals td { border:1px solid #ccc; padding:6px 8px; }
    .meta-table th { width: 160px; }
  </style>
</head>
<body>
  @php
    $fmt = fn($n) => number_format((float)$n, 2, ',', '.');
    $fecha = $order->paid_at ? $order->paid_at : $order->created_at;
  @endphp

  <h1>Postres María José</h1>
  <p class="muted">Factura electrónica (simulada)</p>

  <table class="meta-table">
    <tr>
      <th>Referencia</th>
      <td>{{ $order->referencia }}</td>
    </tr>
    <tr>
      <th>Fecha</th>
      <td>{{ $fecha->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
      <th>Método de pago</th>
      <td>{{ optional($order->paymentMethod)->nombre ?? 'N/D' }}</td>
    </tr>
    <tr>
      <th>Estado</th>
      <td>{{ ucfirst($order->status) }}</td>
    </tr>
    <tr>
      <th>Notas</th>
      <td>{{ $order->notas ?: '—' }}</td>
    </tr>
    @isset($email)
    <tr>
      <th>Correo</th>
      <td>{{ $email }}</td>
    </tr>
    @endisset
  </table>

  @if($order->items && $order->items->count())
    <h3>Detalle</h3>
    <table>
      <thead>
        <tr>
          <th>Producto</th>
          <th class="right">Cantidad</th>
          <th class="right">Precio</th>
          <th class="right">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $it)
          <tr>
            <td>{{ optional($it->producto)->nombre ?? ('#'.$it->producto_id) }}</td>
            <td class="right">{{ $it->cantidad }}</td>
            <td class="right">$ {{ $fmt($it->precio_unitario) }}</td>
            <td class="right">$ {{ $fmt($it->subtotal) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="totals">
      <tr>
        <th class="right" style="width:80%;">Total</th>
        <td class="right" style="width:20%;">$ {{ $fmt($order->total) }}</td>
      </tr>
    </table>
  @else
    <h3>Resumen</h3>
    <table>
      <thead>
        <tr>
          <th>Descripción</th>
          <th class="right">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Compra simulada</td>
          <td class="right">$ {{ $fmt($order->total) }}</td>
        </tr>
      </tbody>
    </table>
  @endif

  <p style="margin-top:16px;">¡Gracias por su compra!</p>
</body>
</html>
