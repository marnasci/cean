<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    protected $fillable = [
        'pessoa_id',
        'data_agendada',
        'notificacao_enviada',
        'observacao',
    ];

    protected $casts = [
        'data_agendada' => 'date',
        'notificacao_enviada' => 'boolean',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }
}
