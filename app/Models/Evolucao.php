<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evolucao extends Model
{
    protected $table = 'evolucoes';

    protected $fillable = [
        'pessoa_id',
        'atendimento_id',
        'user_id',
        'observacao',
        'avaliacao',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
