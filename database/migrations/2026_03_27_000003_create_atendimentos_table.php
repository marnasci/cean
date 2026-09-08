<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('tipo', ['APLICACAO', 'ATENDIMENTO']);
            $table->enum('prioridade', ['ALTA', 'MEDIA', 'BAIXA'])->default('BAIXA');
            $table->enum('status', ['AGUARDANDO', 'CHAMADO', 'EM_ATENDIMENTO', 'CONCLUIDO'])->default('AGUARDANDO');
            $table->integer('ordem_chegada')->default(0);
            $table->dateTime('chamado_em')->nullable();
            $table->dateTime('concluido_em')->nullable();
            $table->date('data_sessao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atendimentos');
    }
};
