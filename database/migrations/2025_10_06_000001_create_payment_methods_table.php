<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();        // 'efectivo', 'nequi', 'bancolombia', 'datafono'
            $table->text('descripcion')->nullable(); // texto corto
            $table->text('instrucciones')->nullable(); // cómo pagar
            $table->boolean('activo')->default(true);
            $table->json('data')->nullable();        // campos extra opcionales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
