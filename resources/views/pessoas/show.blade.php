<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $pessoa->nome_completo }}
                <span class="text-sm font-normal text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full ml-2">{{ $pessoa->codigo }}</span>
            </h2>
            <a href="{{ route('evolucoes.index', $pessoa) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition mr-2">
                📈 Evolução
            </a>
            <a href="{{ route('pessoas.edit', $pessoa) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Dados Pessoais -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Dados Pessoais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Endereço</span>
                        <p class="text-gray-800">{{ $pessoa->endereco ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">WhatsApp</span>
                        <p class="text-gray-800">{{ $pessoa->whatsapp ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Data de Nascimento</span>
                        <p class="text-gray-800">{{ $pessoa->data_nascimento ? $pessoa->data_nascimento->format('d/m/Y') . ' (' . $pessoa->idade . ' anos)' : 'Não informado' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Gênero</span>
                        <p class="text-gray-800">{{ $pessoa->genero ?? 'Não informado' }}</p>
                    </div>
                    @if($pessoa->observacoes)
                        <div class="md:col-span-2">
                            <span class="text-sm text-gray-500">Observações</span>
                            <p class="text-gray-800">{{ $pessoa->observacoes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fichas de Acolhimento -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📝 Fichas de Acolhimento</h3>

                @forelse($pessoa->fichas as $ficha)
                    <div class="mb-4 p-4 bg-indigo-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">{{ $ficha->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-800">{{ $ficha->motivo_busca }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">Nenhuma ficha registrada.</p>
                @endforelse

                <div class="mt-4 border-t pt-4">
                    <form method="POST" action="{{ route('pessoas.ficha', $pessoa) }}">
                        @csrf
                        <label for="motivo_busca" class="block text-sm font-medium text-gray-700">Adicionar nova ficha</label>
                        <textarea name="motivo_busca" id="motivo_busca" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Descreva o motivo..."></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                            Adicionar Ficha
                        </button>
                    </form>
                </div>
            </div>

            <!-- Histórico de Atendimentos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Histórico de Atendimentos</h3>

                @forelse($pessoa->atendimentos as $atendimento)
                    <div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded-lg">
                        <div>
                            <span class="font-medium text-gray-800">{{ $atendimento->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO' }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ $atendimento->data_sessao->format('d/m/Y') }}</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $atendimento->status === 'CONCLUIDO' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ str_replace('_', ' ', $atendimento->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500">Nenhum atendimento registrado.</p>
                @endforelse
            </div>

            <!-- Agendamentos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">📅 Agendamentos</h3>
                    <a href="{{ route('agendamentos.create', ['pessoa_id' => $pessoa->id]) }}" class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                        + Agendar
                    </a>
                </div>

                @forelse($pessoa->agendamentos as $agendamento)
                    <div class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded-lg">
                        <div>
                            <span class="font-medium text-gray-800">{{ $agendamento->data_agendada->format('d/m/Y') }}</span>
                            @if($agendamento->observacao)
                                <span class="text-sm text-gray-500 ml-2">{{ $agendamento->observacao }}</span>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $agendamento->notificacao_enviada ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $agendamento->notificacao_enviada ? '✅ Notificado' : '⏳ Pendente' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500">Nenhum agendamento registrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
