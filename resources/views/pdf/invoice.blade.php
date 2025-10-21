<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura {{ $order->referencia }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#333; }
    h1 { font-size: 18px; margin-bottom: 0; }
    .muted { color:#777; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border:1px solid #ccc; padding:8px; text-align:left; }
    .right { text-align: right; }
  </style>
</head>
<body>
  <h1>Postres María José</h1>
  <p class="muted">Factura electrónica (simulada)</p>

  <table>
    <tr>
      <th>Referencia</th>
      <td>{{ $order->referencia }}</td>
    </tr>
    <tr>
      <th>Fecha</th>
      <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
      <th>Método de pago</th>
      <td>{{ $order->paymentMethod->nombre ?? 'N/D' }}</td>
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

  <h3 style="margin-top:16px;">Resumen</h3>
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
        <td class="right">${{ number_format($order->total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top:16px;">¡Gracias por su compra!</p>
</body>
</html>
