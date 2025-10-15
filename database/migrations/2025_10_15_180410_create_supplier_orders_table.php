<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('data_encomenda')->nullable();
            $table->foreignId('fornecedor_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
    }
};
