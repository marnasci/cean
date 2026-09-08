<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agendamentos</h2>
            <a href="{{ route('agendamentos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Novo Agendamento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtro por data -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('agendamentos.index') }}" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                        <input type="date" name="data" value="{{ $data }}"
                               class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Filtrar
                    </button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        📅 Agendamentos para {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                    </h3>

                    @forelse($agendamentos as $agendamento)
                        <div class="flex items-center justify-between p-4 mb-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $agendamento->pessoa->nome_completo }}</p>
                                <p class="text-sm text-gray-500">
                                    <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded text-xs">{{ $agendamento->pessoa->codigo }}</span>
                                    @if($agendamento->pessoa->whatsapp) · 📱 {{ $agendamento->pessoa->whatsapp }} @endif
                                    @if($agendamento->observacao) · {{ $agendamento->observacao }} @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $agendamento->notificacao_enviada ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $agendamento->notificacao_enviada ? '✅ Notificado' : '⏳ Pendente' }}
                                </span>
                                <form method="POST" action="{{ route('agendamentos.destroy', $agendamento) }}" onsubmit="return confirm('Remover este agendamento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        Remover
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">Nenhum agendamento para esta data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
