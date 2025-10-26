<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre','descripcion','precio','precio_venta','stock','imagen','categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class);
    }

    /** Filtros: búsqueda, categoría y precio */
    public function scopeFiltrar($q, $buscar = null, $categoriaId = null, $min = null, $max = null)
    {
        return $q
            // nombre o descripción
            ->when($buscar, fn($qq) =>
                $qq->where(fn($w) =>
                    $w->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%")
                )
            )
            // categoría
            ->when($categoriaId, fn($qq) => $qq->where('categoria_id', $categoriaId))
            // precio mínimo y/o máximo (usa precio_venta)
            ->when($min !== null && $min !== '', fn($qq) => $qq->where('precio_venta', '>=', $min))
            ->when($max !== null && $max !== '', fn($qq) => $qq->where('precio_venta', '<=', $max));
    }
}




