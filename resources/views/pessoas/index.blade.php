<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pessoas Cadastradas</h2>
            <a href="{{ route('pessoas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                + Novo Cadastro
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Busca -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('pessoas.index') }}" class="flex gap-4">
                    <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome, código ou WhatsApp..."
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Buscar
                    </button>
                    @if($busca)
                        <a href="{{ route('pessoas.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Lista -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gênero</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pessoas as $pessoa)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $pessoa->codigo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $pessoa->nome_completo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $pessoa->whatsapp ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $pessoa->genero ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('pessoas.show', $pessoa) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ver</a>
                                    <a href="{{ route('pessoas.edit', $pessoa) }}" class="text-yellow-600 hover:text-yellow-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    @if($busca)
                                        Nenhuma pessoa encontrada para "{{ $busca }}".
                                    @else
                                        Nenhuma pessoa cadastrada ainda. <a href="{{ route('pessoas.create') }}" class="text-indigo-600 hover:underline">Cadastrar a primeira!</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($pessoas->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $pessoas->appends(['busca' => $busca])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
