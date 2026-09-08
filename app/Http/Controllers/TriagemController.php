<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
    /**
     * Painel principal da triagem - mostra fila do dia
     */
    public function index()
    {
        $hoje = now()->toDateString();

        $aguardando = Atendimento::with('pessoa')
            ->hoje()
            ->status('AGUARDANDO')
            ->ordenadoPorPrioridade()
            ->get();

        $chamados = Atendimento::with('pessoa')
            ->hoje()
            ->status('CHAMADO')
            ->orderBy('chamado_em', 'desc')
            ->get();

        $emAtendimento = Atendimento::with('pessoa')
            ->hoje()
            ->status('EM_ATENDIMENTO')
            ->orderBy('chamado_em', 'desc')
            ->get();

        $concluidos = Atendimento::with('pessoa')
            ->hoje()
            ->status('CONCLUIDO')
            ->orderBy('concluido_em', 'desc')
            ->get();

        return view('triagem.index', compact('aguardando', 'chamados', 'emAtendimento', 'concluidos', 'hoje'));
    }

    /**
     * Tela de check-in - busca pessoa e adiciona na fila
     */
    public function checkin(Request $request)
    {
        $busca = $request->get('busca');
        $pessoas = null;

        if ($busca) {
            $pessoas = Pessoa::where('nome_completo', 'like', "%{$busca}%")
                ->orWhere('codigo', 'like', "%{$busca}%")
                ->limit(10)
                ->get();
        }

        return view('triagem.checkin', compact('busca', 'pessoas'));
    }

    /**
     * Adicionar pessoa na fila do dia
     */
    public function adicionarFila(Request $request)
    {
        $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'prioridade' => 'required|in:ALTA,MEDIA,BAIXA',
            'tipo' => 'required|in:APLICACAO,ATENDIMENTO',
        ]);

        $hoje = now()->toDateString();

        // Verificar se a pessoa já está na fila do dia
        $jaExiste = Atendimento::where('pessoa_id', $request->pessoa_id)
            ->whereDate('data_sessao', $hoje)
            ->whereIn('status', ['AGUARDANDO', 'CHAMADO', 'EM_ATENDIMENTO'])
            ->exists();

        if ($jaExiste) {
            return redirect()->route('triagem.index')
                ->with('error', 'Esta pessoa já está na fila de hoje!');
        }

        Atendimento::create([
            'pessoa_id' => $request->pessoa_id,
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'prioridade' => $request->prioridade,
            'status' => 'AGUARDANDO',
            'ordem_chegada' => Atendimento::proximaOrdem(),
            'data_sessao' => $hoje,
        ]);

        return redirect()->route('triagem.index')
            ->with('success', 'Pessoa adicionada na fila!');
    }

    /**
     * Chamar próxima pessoa (muda status para CHAMADO)
     */
    public function chamar(Atendimento $atendimento)
    {
        $atendimento->update([
            'status' => 'CHAMADO',
            'chamado_em' => now(),
        ]);

        return redirect()->route('triagem.index')
            ->with('success', 'Pessoa chamada!');
    }

    /**
     * Iniciar atendimento (muda status para EM_ATENDIMENTO)
     */
    public function iniciarAtendimento(Atendimento $atendimento)
    {
        $atendimento->update([
            'status' => 'EM_ATENDIMENTO',
        ]);

        return redirect()->route('triagem.index')
            ->with('success', 'Atendimento iniciado!');
    }

    /**
     * Concluir atendimento
     */
    public function concluir(Atendimento $atendimento)
    {
        $atendimento->update([
            'status' => 'CONCLUIDO',
            'concluido_em' => now(),
        ]);

        return redirect()->route('triagem.index')
            ->with('success', 'Atendimento concluído!');
    }

    /**
     * API para o telão - retorna dados em JSON
     */
    public function apiTelao()
    {
        $hoje = now()->toDateString();

        $chamados = Atendimento::with('pessoa')
            ->whereDate('data_sessao', $hoje)
            ->whereIn('status', ['CHAMADO', 'EM_ATENDIMENTO'])
            ->orderBy('chamado_em', 'desc')
            ->get()
            ->map(function ($atendimento) {
                return [
                    'id' => $atendimento->id,
                    'nome' => $atendimento->pessoa->nome_completo,
                    'tipo' => $atendimento->tipo,
                    'status' => $atendimento->status,
                    'chamado_em' => $atendimento->chamado_em->format('H:i'),
                ];
            });

        return response()->json($chamados);
    }
}
