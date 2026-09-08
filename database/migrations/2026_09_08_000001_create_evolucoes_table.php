<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evolucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('atendimento_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('observacao');
            $table->unsignedTinyInteger('avaliacao')->comment('1 a 5 estrelas');
            $table->timestamps();

            $table->index(['pessoa_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evolucoes');
    }
};
