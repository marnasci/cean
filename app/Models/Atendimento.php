<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atendimento extends Model
{
    protected $fillable = [
        'pessoa_id',
        'user_id',
        'tipo',
        'prioridade',
        'status',
        'ordem_chegada',
        'chamado_em',
        'concluido_em',
        'data_sessao',
    ];

    protected $casts = [
        'chamado_em' => 'datetime',
        'concluido_em' => 'datetime',
        'data_sessao' => 'date',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para ordenar pela prioridade e ordem de chegada.
     * ALTA vem primeiro, depois MEDIA, depois BAIXA.
     * Dentro de cada prioridade, quem chegou primeiro é atendido primeiro.
     */
    public function scopeOrdenadoPorPrioridade(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE prioridade
                WHEN 'ALTA' THEN 1
                WHEN 'MEDIA' THEN 2
                WHEN 'BAIXA' THEN 3
            END
        ")->orderBy('ordem_chegada', 'asc');
    }

    /**
     * Scope para atendimentos do dia
     */
    public function scopeHoje(Builder $query): Builder
    {
        return $query->whereDate('data_sessao', now()->toDateString());
    }

    /**
     * Scope por status
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Retorna o próximo número de ordem para hoje
     */
    public static function proximaOrdem(): int
    {
        return (self::hoje()->max('ordem_chegada') ?? 0) + 1;
    }
}
