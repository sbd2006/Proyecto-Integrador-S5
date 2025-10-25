<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'payment_method_id',
        'status',
        'referencia',
        'notas',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total'   => 'decimal:2',
    ];

    // Relaciones
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detalle de la orden (líneas de productos).
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes útiles
    public function scopePagadas($q)
    {
        return $q->where('status', 'pagado');
    }

    /**
     * Filtra por intervalo usando paid_at (o created_at si se indica).
     */
    public function scopeEntreFechas($q, $desde, $hasta, bool $usarPaidAt = true)
    {
        $col = $usarPaidAt ? 'paid_at' : 'created_at';

        $desde = $desde instanceof Carbon ? $desde->copy()->startOfDay() : Carbon::parse($desde)->startOfDay();
        $hasta = $hasta instanceof Carbon ? $hasta->copy()->endOfDay()   : Carbon::parse($hasta)->endOfDay();

        return $q->when($usarPaidAt, fn($qq) => $qq->whereNotNull('paid_at'))
                 ->whereBetween($col, [$desde, $hasta]);
    }
}
