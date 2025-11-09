<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();

            $table->unique('nombre', 'categorias_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};