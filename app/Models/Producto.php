<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'precio_venta',
        'stock',
        'imagen',
        // 'id_categoria', ← cuando tus compañeros creen esa parte
    ];
}

