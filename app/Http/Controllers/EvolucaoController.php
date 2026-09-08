<?php

namespace App\Http\Controllers;

use App\Models\Evolucao;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class EvolucaoController extends Controller
{
    /**
     * Tela de histórico / evolução de uma pessoa
     */
    public function index(Pessoa $pessoa)
    {
        $pessoa->load(['fichas', 'atendimentos' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        $evolucoes = Evolucao::where('pessoa_id', $pessoa->id)
            ->with('user', 'atendimento')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('evolucoes.index', compact('pessoa', 'evolucoes'));
    }

    /**
     * Salvar nova evolução
     */
    public function store(Request $request, Pessoa $pessoa)
    {
        $validated = $request->validate([
            'observacao' => 'required|string',
            'avaliacao' => 'required|integer|min:1|max:5',
            'atendimento_id' => 'nullable|exists:atendimentos,id',
        ]);

        Evolucao::create([
            'pessoa_id' => $pessoa->id,
            'user_id' => auth()->id(),
            'observacao' => $validated['observacao'],
            'avaliacao' => $validated['avaliacao'],
            'atendimento_id' => $validated['atendimento_id'] ?? null,
        ]);

        return redirect()->route('evolucoes.index', $pessoa)
            ->with('success', 'Evolução registrada com sucesso!');
    }
}
