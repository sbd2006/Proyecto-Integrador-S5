<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
        $table->decimal('total', 10, 2);
        $table->enum('estado', ['pendiente','en_preparacion','listo','entregado','cancelado'])->default('pendiente');
        $table->string('direccion_entrega')->nullable();
        $table->string('metodo_pago')->nullable();
        $table->text('nota')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
