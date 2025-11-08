<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            $table->decimal('total', 10, 2);
            $table->enum('estado', [
                'pendiente',
                'en_preparacion',
                'listo',
                'entregado',
                'cancelado'
            ])->default('pendiente');
            $table->string('direccion_entrega')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->text('nota')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
