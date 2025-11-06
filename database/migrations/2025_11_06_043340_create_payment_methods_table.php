<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('slug');
            $table->text('descripcion')->nullable();
            $table->text('instrucciones')->nullable();
            $table->boolean('activo')->default(1);
            $table->longText('data')->nullable(); // En tu SQL tiene CHECK JSON, aquí lo dejamos como longText
            $table->timestamps();

            $table->unique('slug', 'payment_methods_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
