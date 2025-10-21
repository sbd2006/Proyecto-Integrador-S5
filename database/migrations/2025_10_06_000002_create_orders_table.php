<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('total', 10, 2)->default(0);
            $t->foreignId('payment_method_id')->constrained('payment_methods');
            $t->string('status')->default('pendiente'); // pendiente|pagado|fallido|cancelado
            $t->string('referencia')->unique();
            $t->text('notas')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
