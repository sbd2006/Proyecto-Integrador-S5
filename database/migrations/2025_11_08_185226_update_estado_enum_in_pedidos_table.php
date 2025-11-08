<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiar el ENUM del campo 'estado' para agregar "pagado"
        DB::statement("
            ALTER TABLE pedidos 
            MODIFY COLUMN estado ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado', 'pagado') 
            NOT NULL DEFAULT 'pendiente'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al ENUM anterior (sin 'pagado')
        DB::statement("
            ALTER TABLE pedidos 
            MODIFY COLUMN estado ENUM('pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado') 
            NOT NULL DEFAULT 'pendiente'
        ");
    }
};
