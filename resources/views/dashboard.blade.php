<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel - CEAN
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Cadastros -->
                <a href="{{ route('pessoas.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="text-3xl mb-2">👤</div>
                        <h3 class="text-lg font-semibold text-gray-800">Cadastro de Pessoas</h3>
                        <p class="text-sm text-gray-500 mt-1">Cadastrar e gerenciar pessoas</p>
                    </div>
                </a>

                <!-- Triagem -->
                <a href="{{ route('triagem.checkin') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="text-3xl mb-2">📋</div>
                        <h3 class="text-lg font-semibold text-gray-800">Check-in</h3>
                        <p class="text-sm text-gray-500 mt-1">Registrar chegada de pessoas</p>
                    </div>
                </a>

                <!-- Fila -->
                <a href="{{ route('triagem.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="text-3xl mb-2">📊</div>
                        <h3 class="text-lg font-semibold text-gray-800">Fila de Triagem</h3>
                        <p class="text-sm text-gray-500 mt-1">Gerenciar fila de atendimento</p>
                    </div>
                </a>

                <!-- Agendamentos -->
                <a href="{{ route('agendamentos.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="text-3xl mb-2">📅</div>
                        <h3 class="text-lg font-semibold text-gray-800">Agendamentos</h3>
                        <p class="text-sm text-gray-500 mt-1">Gerenciar agendamentos</p>
                    </div>
                </a>
            </div>

            <!-- Atalho Telão -->
            <div class="bg-indigo-600 overflow-hidden shadow-sm sm:rounded-lg">
                <a href="{{ route('telao') }}" target="_blank" class="block p-6 text-center">
                    <div class="text-4xl mb-2">📺</div>
                    <h3 class="text-xl font-bold text-white">Abrir Telão Público</h3>
                    <p class="text-indigo-200 mt-1">Abre em nova aba - ideal para TV ou projetor</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
