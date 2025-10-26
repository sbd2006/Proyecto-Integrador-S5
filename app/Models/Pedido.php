<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['cliente_id','total','estado','direccion_entrega','metodo_pago','nota'];

    public function cliente()
    {
        return $this->belongsTo(\App\Models\User::class, 'cliente_id');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}
