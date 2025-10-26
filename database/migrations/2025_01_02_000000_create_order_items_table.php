<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
                $table->unsignedInteger('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->index(['order_id', 'producto_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
