<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif">
  <h2>¡Gracias por tu compra! 🧾</h2>
  <p>
    Referencia: <strong>{{ $order->referencia }}</strong><br>
    Método de pago: <strong>{{ $order->paymentMethod?->nombre }}</strong><br>
    Total: <strong>${{ number_format($order->total, 2) }}</strong><br>
    Estado: <strong>{{ ucfirst($order->status) }}</strong>
  </p>
  <p>Adjuntamos tu factura en PDF.</p>
  <small>Postres María José</small>
</body>
</html>
