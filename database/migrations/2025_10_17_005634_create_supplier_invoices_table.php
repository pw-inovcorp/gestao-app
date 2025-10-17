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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('data_fatura');
            $table->date('data_vencimento');
            $table->foreignId('fornecedor_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('supplier_order_id')->nullable()->constrained('supplier_orders')->nullOnDelete();
            $table->decimal('valor_total', 10, 2)->unsigned();
            $table->string('documento')->nullable();
            $table->string('comprovativo_pagamento')->nullable();
            $table->enum('estado', ['pendente_pagamento', 'paga'])->default('pendente_pagamento');
            $table->date('data_pagamento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
