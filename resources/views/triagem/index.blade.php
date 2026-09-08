<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Painel de Triagem — {{ \Carbon\Carbon::parse($hoje)->format('d/m/Y') }}
            </h2>
            <a href="{{ route('triagem.checkin') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                + Check-in
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- AGUARDANDO -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-yellow-500 px-4 py-3">
                        <h3 class="font-bold text-white text-center">⏳ AGUARDANDO ({{ $aguardando->count() }})</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                        @forelse($aguardando as $item)
                            <div class="border rounded-lg p-3 {{ $item->prioridade === 'ALTA' ? 'border-red-300 bg-red-50' : ($item->prioridade === 'MEDIA' ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200 bg-gray-50') }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $item->pessoa->nome_completo }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->pessoa->codigo }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                        {{ $item->prioridade === 'ALTA' ? 'bg-red-600 text-white' : ($item->prioridade === 'MEDIA' ? 'bg-yellow-500 text-white' : 'bg-gray-400 text-white') }}">
                                        {{ $item->prioridade }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-xs px-2 py-0.5 rounded {{ $item->tipo === 'APLICACAO' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $item->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO' }}
                                    </span>
                                    <span class="text-xs text-gray-400">#{{ $item->ordem_chegada }}</span>
                                </div>
                                <form method="POST" action="{{ route('triagem.chamar', $item) }}">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-1.5 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700 transition">
                                        📢 Chamar
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-8">Nenhuma pessoa aguardando</p>
                        @endforelse
                    </div>
                </div>

                <!-- CHAMADOS -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-blue-500 px-4 py-3">
                        <h3 class="font-bold text-white text-center">📢 CHAMADOS ({{ $chamados->count() }})</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                        @forelse($chamados as $item)
                            <div class="border border-blue-200 bg-blue-50 rounded-lg p-3">
                                <p class="font-semibold text-gray-800 text-sm">{{ $item->pessoa->nome_completo }}</p>
                                <p class="text-xs text-gray-500">{{ $item->pessoa->codigo }} · Chamado às {{ $item->chamado_em->format('H:i') }}</p>
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded {{ $item->tipo === 'APLICACAO' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $item->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO' }}
                                </span>
                                <form method="POST" action="{{ route('triagem.iniciar', $item) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-1.5 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition">
                                        ✅ Iniciar {{ $item->tipo === 'APLICACAO' ? 'Aplicação' : 'Atendimento' }}
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-8">Nenhuma pessoa chamada</p>
                        @endforelse
                    </div>
                </div>

                <!-- EM ATENDIMENTO -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-green-500 px-4 py-3">
                        <h3 class="font-bold text-white text-center">🔄 EM ATENDIMENTO ({{ $emAtendimento->count() }})</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                        @forelse($emAtendimento as $item)
                            <div class="border border-green-200 bg-green-50 rounded-lg p-3">
                                <p class="font-semibold text-gray-800 text-sm">{{ $item->pessoa->nome_completo }}</p>
                                <p class="text-xs text-gray-500">{{ $item->pessoa->codigo }}</p>
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded {{ $item->tipo === 'APLICACAO' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $item->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO' }}
                                </span>
                                <div class="mt-2 flex gap-2">
                                    <form method="POST" action="{{ route('triagem.concluir', $item) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 bg-gray-700 text-white rounded text-xs hover:bg-gray-800 transition">
                                            ✔ Concluir
                                        </button>
                                    </form>
                                    @if($item->tipo === 'ATENDIMENTO')
                                        <a href="{{ route('agendamentos.create', ['pessoa_id' => $item->pessoa_id]) }}" class="flex-1 px-3 py-1.5 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700 transition text-center">
                                            📅 Agendar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-8">Ninguém em atendimento</p>
                        @endforelse
                    </div>
                </div>

                <!-- CONCLUÍDOS -->
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-500 px-4 py-3">
                        <h3 class="font-bold text-white text-center">✔ CONCLUÍDOS ({{ $concluidos->count() }})</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                        @forelse($concluidos as $item)
                            <div class="border border-gray-200 bg-gray-50 rounded-lg p-3">
                                <p class="font-semibold text-gray-800 text-sm">{{ $item->pessoa->nome_completo }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $item->tipo === 'APLICACAO' ? 'APLICAÇÃO' : 'ATENDIMENTO' }}
                                    · Concluído às {{ $item->concluido_em->format('H:i') }}
                                </p>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-sm py-8">Nenhum atendimento concluído</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
