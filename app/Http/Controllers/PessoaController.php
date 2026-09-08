<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Ficha;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->get('busca');

        $pessoas = Pessoa::query()
            ->when($busca, function ($query) use ($busca) {
                $query->where('nome_completo', 'like', "%{$busca}%")
                      ->orWhere('codigo', 'like', "%{$busca}%")
                      ->orWhere('whatsapp', 'like', "%{$busca}%");
            })
            ->orderBy('nome_completo')
            ->paginate(20);

        return view('pessoas.index', compact('pessoas', 'busca'));
    }

    public function create()
    {
        return view('pessoas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|in:Masculino,Feminino,Outro',
            'observacoes' => 'nullable|string',
            'motivo_busca' => 'nullable|string',
        ]);

        $pessoa = Pessoa::create($validated);

        if ($request->filled('motivo_busca')) {
            Ficha::create([
                'pessoa_id' => $pessoa->id,
                'motivo_busca' => $request->motivo_busca,
            ]);
        }

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Pessoa cadastrada com sucesso! Código: ' . $pessoa->codigo);
    }

    public function show(Pessoa $pessoa)
    {
        $pessoa->load(['fichas', 'atendimentos' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }, 'agendamentos' => function ($q) {
            $q->orderBy('data_agendada', 'desc');
        }]);

        return view('pessoas.show', compact('pessoa'));
    }

    public function edit(Pessoa $pessoa)
    {
        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(Request $request, Pessoa $pessoa)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|in:Masculino,Feminino,Outro',
            'observacoes' => 'nullable|string',
        ]);

        $pessoa->update($validated);

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Dados atualizados com sucesso!');
    }

    public function adicionarFicha(Request $request, Pessoa $pessoa)
    {
        $request->validate([
            'motivo_busca' => 'required|string',
        ]);

        Ficha::create([
            'pessoa_id' => $pessoa->id,
            'motivo_busca' => $request->motivo_busca,
        ]);

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Ficha adicionada com sucesso!');
    }
}
