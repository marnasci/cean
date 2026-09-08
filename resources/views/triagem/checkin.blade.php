<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Check-in — Registrar Chegada</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Busca -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔍 Buscar Pessoa</h3>
                <form method="GET" action="{{ route('triagem.checkin') }}" class="flex gap-4">
                    <input type="text" name="busca" value="{{ $busca }}" placeholder="Digite o nome ou código (ex: CEAN-0001)..."
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg"
                           autofocus>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-lg">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Resultados -->
            @if($pessoas && $pessoas->count() > 0)
                <div class="space-y-4">
                    @foreach($pessoas as $pessoa)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $pessoa->nome_completo }}</h4>
                                    <p class="text-sm text-gray-500">
                                        <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded text-xs font-medium">{{ $pessoa->codigo }}</span>
                                        @if($pessoa->whatsapp) · 📱 {{ $pessoa->whatsapp }} @endif
                                    </p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('triagem.fila') }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="pessoa_id" value="{{ $pessoa->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Prioridade -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
                                        <select name="prioridade" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="BAIXA">🟢 BAIXA</option>
                                            <option value="MEDIA">🟡 MÉDIA</option>
                                            <option value="ALTA">🔴 ALTA</option>
                                        </select>
                                    </div>

                                    <!-- Tipo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Direcionamento</label>
                                        <select name="tipo" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="APLICACAO">💧 APLICAÇÃO</option>
                                            <option value="ATENDIMENTO">🗣️ ATENDIMENTO</option>
                                        </select>
                                    </div>

                                    <div class="flex items-end">
                                        <button type="submit" class="w-full px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-semibold">
                                            ✅ Adicionar à Fila
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @elseif($busca)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 mb-4">Nenhuma pessoa encontrada para "{{ $busca }}".</p>
                    <a href="{{ route('pessoas.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Cadastrar Nova Pessoa
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
