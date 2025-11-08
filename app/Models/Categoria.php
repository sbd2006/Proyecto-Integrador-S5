<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','descripcion','estado'];

    public function scopeFiltrar($q, $buscar = null, $estado = null)
    {
        if ($buscar) {
            $q->where(fn($w) => $w
                ->where('nombre', 'like', "%$buscar%")
                ->orWhere('descripcion', 'like', "%$buscar%"));
        }
        if ($estado !== null && $estado !== '') {
            $q->where('estado', (bool)$estado);
        }
        return $q;
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
