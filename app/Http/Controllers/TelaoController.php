<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use Illuminate\Http\Request;

class TelaoController extends Controller
{
    /**
     * Tela pública do telão (sem autenticação)
     */
    public function index()
    {
        return view('telao.index');
    }

    /**
     * API JSON para atualização do telão
     */
    public function dados()
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
                    'nome' => strtoupper($atendimento->pessoa->nome_completo),
                    'tipo' => $atendimento->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO',
                    'tipo_raw' => $atendimento->tipo,
                    'status' => $atendimento->status,
                    'hora' => $atendimento->chamado_em ? $atendimento->chamado_em->format('H:i') : '--:--',
                ];
            });

        return response()->json([
            'chamados' => $chamados,
            'data' => now()->format('d/m/Y'),
            'hora' => now()->format('H:i:s'),
        ]);
    }
}
