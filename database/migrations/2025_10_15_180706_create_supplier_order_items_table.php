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
        Schema::create('supplier_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_order_id')->constrained('supplier_orders')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->integer('quantidade')->default(1)->unsigned();
            $table->decimal('preco_unitario', 10, 2)->unsigned();
            $table->decimal('subtotal', 10, 2)->unsigned();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_order_items');
    }
};
