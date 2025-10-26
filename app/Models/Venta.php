<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    // Nombre de la tabla (si no sigue la convención 'ventas')
    protected $table = 'ventas';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'total',
        'estado', // pendiente, pagado, cancelado, etc.
        'fecha',
    ];

    // 🔗 Relación: una venta pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔗 Relación: una venta tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    // 💡 Accesor para formatear el total (opcional)
    public function getTotalFormateadoAttribute()
    {
        return number_format($this->total, 2);
    }

    // 💡 Accesor para mostrar estado legible
    public function getEstadoTextoAttribute()
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente de pago',
            'pagado' => 'Pagado',
            'cancelado' => 'Cancelado',
            default => ucfirst($this->estado ?? 'Desconocido'),
        };
    }
}
