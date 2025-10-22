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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->date('data');
            $table->time('hora');
            $table->integer('duracao')->default(60)->comment('Duração em minutos');

            $table->json('partilha')->nullable()->comment('Configurações de partilha (quem pode ver/editar)');
            $table->text('conhecimento')->nullable()->comment('Insights/aprendizagens do evento');
            $table->text('descricao')->nullable();

            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete();
            $table->foreignId('calendar_type_id')->nullable()->constrained('calendar_types')->nullOnDelete();
            $table->foreignId('calendar_action_id')->nullable()->constrained('calendar_actions')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();

            $table->index('data');
            $table->index(['user_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
