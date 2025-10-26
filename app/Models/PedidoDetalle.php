<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $fillable = ['pedido_id','producto_id','cantidad','precio_unitario','subtotal'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
