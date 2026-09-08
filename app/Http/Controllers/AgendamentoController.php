<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Lista agendamentos do dia ou por data
     */
    public function index(Request $request)
    {
        $data = $request->get('data', now()->toDateString());

        $agendamentos = Agendamento::with('pessoa')
            ->where('data_agendada', $data)
            ->orderBy('created_at')
            ->get();

        return view('agendamentos.index', compact('agendamentos', 'data'));
    }

    /**
     * Formulário de agendamento para uma pessoa
     */
    public function create(Request $request)
    {
        $pessoa = null;
        if ($request->has('pessoa_id')) {
            $pessoa = Pessoa::findOrFail($request->pessoa_id);
        }

        return view('agendamentos.create', compact('pessoa'));
    }

    /**
     * Salvar agendamento
     */
    public function store(Request $request)
    {
        $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'data_agendada' => 'required|date|after:today',
            'observacao' => 'nullable|string',
        ]);

        $agendamento = Agendamento::create([
            'pessoa_id' => $request->pessoa_id,
            'data_agendada' => $request->data_agendada,
            'observacao' => $request->observacao,
        ]);

        return redirect()->route('agendamentos.index')
            ->with('success', 'Agendamento criado com sucesso para ' . $agendamento->data_agendada->format('d/m/Y'));
    }

    /**
     * Excluir agendamento
     */
    public function destroy(Agendamento $agendamento)
    {
        $agendamento->delete();

        return redirect()->route('agendamentos.index')
            ->with('success', 'Agendamento removido.');
    }
}
