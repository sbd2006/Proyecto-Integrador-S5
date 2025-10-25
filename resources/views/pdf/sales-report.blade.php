@php
    $fmt = fn($n) => number_format((float)$n, 2, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        .header { margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .meta { font-size: 12px; color: #555; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f3f3f3; text-align: left; }
        .right { text-align: right; }
        .muted { color: #666; }
        .summary { margin-top: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Reporte de ventas</p>
        <p class="meta">
            Intervalo: <strong>{{ $desde->format('d/m/Y') }}</strong> — <strong>{{ $hasta->format('d/m/Y') }}</strong><br>
            Estado: <strong>{{ ucfirst($status) }}</strong> |
            Generado: {{ $generado->format('d/m/Y H:i') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Referencia</th>
                <th>Método de pago</th>
                <th>Estado</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $o)
                <tr>
                    <td>
                        @if($status === 'pagado' && $o->paid_at) {{ $o->paid_at->format('d/m/Y H:i') }}
                        @else {{ $o->created_at->format('d/m/Y H:i') }} @endif
                    </td>
                    <td>{{ $o->referencia }}</td>
                    <td>{{ optional($o->paymentMethod)->nombre ?? 'N/D' }}</td>
                    <td>{{ ucfirst($o->status) }}</td>
                    <td class="right">$ {{ $fmt($o->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="muted">No se encontraron órdenes en el intervalo seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <thead>
                <tr>
                    <th>Resumen por método de pago</th>
                    <th class="right"># Órdenes</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porMetodo as $row)
                    <tr>
                        <td>{{ $row['metodo'] }}</td>
                        <td class="right">{{ $row['conteo'] }}</td>
                        <td class="right">$ {{ $fmt($row['total']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">Sin datos para el período.</td>
                    </tr>
                @endforelse
                <tr>
                    <th colspan="2" class="right">Total general</th>
                    <th class="right">$ {{ $fmt($totalGeneral) }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
