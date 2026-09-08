<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pessoa extends Model
{
    protected $fillable = [
        'codigo',
        'nome_completo',
        'endereco',
        'whatsapp',
        'data_nascimento',
        'genero',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pessoa $pessoa) {
            if (empty($pessoa->codigo)) {
                $last = Pessoa::orderBy('id', 'desc')->first();
                $nextId = $last ? $last->id + 1 : 1;
                $pessoa->codigo = 'CEAN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(Ficha::class);
    }

    public function fichaAtual(): HasOne
    {
        return $this->hasOne(Ficha::class)->latestOfMany();
    }

    public function atendimentos(): HasMany
    {
        return $this->hasMany(Atendimento::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
    }
}
