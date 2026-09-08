<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\EvolucaoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelaoController;
use App\Http\Controllers\TriagemController;
use Illuminate\Support\Facades\Route;

// ===== ROTAS PÚBLICAS (sem autenticação) =====

// Telão público para TV
Route::get('/telao', [TelaoController::class, 'index'])->name('telao');
Route::get('/api/telao', [TelaoController::class, 'dados'])->name('api.telao');

// ===== ROTAS AUTENTICADAS =====

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cadastro de Pessoas
    Route::resource('pessoas', PessoaController::class)->except(['destroy']);
    Route::post('/pessoas/{pessoa}/ficha', [PessoaController::class, 'adicionarFicha'])->name('pessoas.ficha');

    // Triagem
    Route::get('/triagem', [TriagemController::class, 'index'])->name('triagem.index');
    Route::get('/triagem/checkin', [TriagemController::class, 'checkin'])->name('triagem.checkin');
    Route::post('/triagem/fila', [TriagemController::class, 'adicionarFila'])->name('triagem.fila');
    Route::post('/triagem/{atendimento}/chamar', [TriagemController::class, 'chamar'])->name('triagem.chamar');
    Route::post('/triagem/{atendimento}/iniciar', [TriagemController::class, 'iniciarAtendimento'])->name('triagem.iniciar');
    Route::post('/triagem/{atendimento}/concluir', [TriagemController::class, 'concluir'])->name('triagem.concluir');

    // Agendamentos
    Route::resource('agendamentos', AgendamentoController::class)->only(['index', 'create', 'store', 'destroy']);

    // Evoluções
    Route::get('/pessoas/{pessoa}/evolucoes', [EvolucaoController::class, 'index'])->name('evolucoes.index');
    Route::post('/pessoas/{pessoa}/evolucoes', [EvolucaoController::class, 'store'])->name('evolucoes.store');
});

require __DIR__.'/auth.php';
