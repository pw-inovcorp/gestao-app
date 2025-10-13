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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('data_proposta')->nullable();
            $table->foreignId('cliente_id')->constrained('entities')->cascadeOnDelete();
            $table->date('validade');
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
