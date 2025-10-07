<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Agregamos la columna si no existe
            if (!Schema::hasColumn('productos', 'categoria_id')) {
                $table->unsignedBigInteger('categoria_id')->after('id');

                // Clave foránea con tabla 'categorias'
                $table->foreign('categoria_id')
                      ->references('id')
                      ->on('categorias')
                      ->onDelete('restrict'); // Evita borrar una categoría con productos
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }
};
