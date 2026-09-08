<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Agendamento</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('agendamentos.store') }}">
                    @csrf

                    <div class="space-y-6">
                        @if($pessoa)
                            <div class="p-4 bg-indigo-50 rounded-lg">
                                <p class="font-semibold text-indigo-800">{{ $pessoa->nome_completo }}</p>
                                <p class="text-sm text-indigo-600">{{ $pessoa->codigo }} @if($pessoa->whatsapp) · 📱 {{ $pessoa->whatsapp }} @endif</p>
                            </div>
                            <input type="hidden" name="pessoa_id" value="{{ $pessoa->id }}">
                        @else
                            <div>
                                <label for="pessoa_id" class="block text-sm font-medium text-gray-700">Pessoa *</label>
                                <select name="pessoa_id" id="pessoa_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    @foreach(\App\Models\Pessoa::orderBy('nome_completo')->get() as $p)
                                        <option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nome_completo }}</option>
                                    @endforeach
                                </select>
                                @error('pessoa_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label for="data_agendada" class="block text-sm font-medium text-gray-700">Data do Agendamento *</label>
                            <input type="date" name="data_agendada" id="data_agendada" value="{{ old('data_agendada') }}" required
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('data_agendada') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                            <textarea name="observacao" id="observacao" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Observações sobre o agendamento...">{{ old('observacao') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('agendamentos.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                Agendar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
