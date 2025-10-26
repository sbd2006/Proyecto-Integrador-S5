<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas'; // o 'detalleventa' según tu migración

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio',
    ];

    // 🔗 Relación: cada detalle pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // 🔗 Relación: cada detalle pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // 💡 Calcular subtotal
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio;
    }
}
