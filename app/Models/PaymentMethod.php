<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory; // opcional

    protected $fillable = ['nombre','slug','descripcion','instrucciones','activo','data'];

    protected $casts = [
        'activo' => 'boolean',
        'data'   => 'array',
    ];

    /** Sólo activos */
    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    /** (Opcional) Buscar por slug */
    public function scopeSlug($q, string $slug)
    {
        return $q->where('slug', $slug);
    }

    /** Relación con órdenes (orders.payment_method_id) */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
