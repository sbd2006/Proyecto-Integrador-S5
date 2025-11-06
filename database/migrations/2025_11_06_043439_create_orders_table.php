<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->unsignedBigInteger('payment_method_id');
            $table->string('status')->default('pendiente');
            $table->timestamp('paid_at')->nullable();
            $table->string('referencia');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique('referencia', 'orders_referencia_unique');

            $table->index('paid_at', 'orders_paid_at_index');
            $table->index('status', 'orders_status_index');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('payment_method_id')
                ->references('id')->on('payment_methods');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
