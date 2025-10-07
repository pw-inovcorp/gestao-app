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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nif')->unique();
            $table->string('nome');
            $table->string('morada')->nullable();
            $table->string('codigo_postal', 8)->nullable();
            $table->string('localidade')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('telefone')->nullable();
            $table->string('telemovel')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->boolean('consentimento_rgpd')->default(false);
            $table->text('observacoes')->nullable();
            $table->boolean('is_cliente')->default(false);
            $table->boolean('is_fornecedor')->default(false);
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
