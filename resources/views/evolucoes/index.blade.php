<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📈 Evolução — {{ $pessoa->nome_completo }}
                <span class="text-sm font-normal text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full ml-2">{{ $pessoa->codigo }}</span>
            </h2>
            <a href="{{ route('pessoas.show', $pessoa) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Formulário de nova evolução --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">✍️ Registrar Nova Evolução</h3>

                <form method="POST" action="{{ route('evolucoes.store', $pessoa) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Lado esquerdo: Observação --}}
                        <div>
                            <label for="observacao" class="block text-sm font-medium text-gray-700 mb-1">Observação / Pontos de Melhoria ou Piora</label>
                            <textarea name="observacao" id="observacao" rows="6" required
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Descreva como o paciente está evoluindo, pontos de melhoria, piora, observações gerais...">{{ old('observacao') }}</textarea>
                            @error('observacao')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Atendimento vinculado (opcional) --}}
                            @if($pessoa->atendimentos->where('status', 'CONCLUIDO')->count())
                                <div class="mt-3">
                                    <label for="atendimento_id" class="block text-sm font-medium text-gray-700 mb-1">Vincular a Atendimento (opcional)</label>
                                    <select name="atendimento_id" id="atendimento_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">— Nenhum —</option>
                                        @foreach($pessoa->atendimentos->where('status', 'CONCLUIDO') as $atendimento)
                                            <option value="{{ $atendimento->id }}">
                                                {{ $atendimento->tipo === 'APLICACAO' ? 'Aplicação' : 'Atendimento' }} — {{ $atendimento->data_sessao->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        {{-- Lado direito: Estrelas --}}
                        <div class="flex flex-col items-center justify-center">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Avaliação Geral</label>

                            <div class="star-rating flex flex-row-reverse gap-1" id="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="avaliacao" id="star-{{ $i }}" value="{{ $i }}" class="hidden peer" {{ old('avaliacao') == $i ? 'checked' : '' }} required>
                                    <label for="star-{{ $i }}" class="star-label cursor-pointer text-5xl text-gray-300 hover:text-yellow-400 transition-colors duration-150" data-value="{{ $i }}">★</label>
                                @endfor
                            </div>

                            <div class="mt-3 text-sm text-gray-500 text-center" id="star-description">
                                Selecione de 1 a 5 estrelas
                            </div>

                            <div class="mt-2 grid grid-cols-5 gap-1 text-xs text-gray-400 w-full max-w-xs text-center">
                                <span>Ruim</span>
                                <span>Regular</span>
                                <span>Bom</span>
                                <span>Muito Bom</span>
                                <span>Excelente</span>
                            </div>

                            @error('avaliacao')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-semibold">
                            💾 Salvar Evolução
                        </button>
                    </div>
                </form>
            </div>

            {{-- Histórico de evoluções --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Histórico de Evolução</h3>

                {{-- Gráfico de evolução --}}
                @if($evolucoes->count() >= 2)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-600 mb-3">Curva de Evolução</h4>
                        <div class="flex items-end gap-2 h-32">
                            @foreach($evolucoes->reverse() as $ev)
                                @php
                                    $height = ($ev->avaliacao / 5) * 100;
                                    $colors = [
                                        1 => 'bg-red-400',
                                        2 => 'bg-orange-400',
                                        3 => 'bg-yellow-400',
                                        4 => 'bg-lime-400',
                                        5 => 'bg-green-400',
                                    ];
                                @endphp
                                <div class="flex flex-col items-center flex-1 min-w-0">
                                    <span class="text-xs text-gray-500 mb-1">{{ $ev->avaliacao }}★</span>
                                    <div class="{{ $colors[$ev->avaliacao] }} rounded-t w-full transition-all duration-300" style="height: {{ $height }}%;" title="{{ $ev->created_at->format('d/m/Y') }}"></div>
                                    <span class="text-[10px] text-gray-400 mt-1 truncate w-full text-center">{{ $ev->created_at->format('d/m') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Lista de evoluções --}}
                @forelse($evolucoes as $evolucao)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border-l-4 {{ $evolucao->avaliacao >= 4 ? 'border-green-400' : ($evolucao->avaliacao >= 3 ? 'border-yellow-400' : 'border-red-400') }}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-sm text-gray-500">{{ $evolucao->created_at->format('d/m/Y H:i') }}</span>
                                <span class="text-sm text-gray-400 ml-2">por {{ $evolucao->user->name }}</span>
                                @if($evolucao->atendimento)
                                    <span class="text-xs text-indigo-500 ml-2 bg-indigo-50 px-2 py-0.5 rounded">
                                        {{ $evolucao->atendimento->tipo === 'APLICACAO' ? 'Aplicação' : 'Atendimento' }} — {{ $evolucao->atendimento->data_sessao->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex gap-0.5 text-xl">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $evolucao->avaliacao ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-800 whitespace-pre-line">{{ $evolucao->observacao }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Nenhuma evolução registrada ainda.</p>
                @endforelse

                {{-- Resumo --}}
                @if($evolucoes->count())
                    <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-indigo-800 mb-2">📋 Resumo</h4>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-indigo-600">{{ $evolucoes->count() }}</div>
                                <div class="text-xs text-gray-500">Registros</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-indigo-600">{{ number_format($evolucoes->avg('avaliacao'), 1) }} ★</div>
                                <div class="text-xs text-gray-500">Média Geral</div>
                            </div>
                            <div>
                                @php
                                    $trend = $evolucoes->count() >= 2
                                        ? $evolucoes->first()->avaliacao - $evolucoes->last()->avaliacao
                                        : 0;
                                @endphp
                                <div class="text-2xl font-bold {{ $trend > 0 ? 'text-green-600' : ($trend < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ $trend > 0 ? '↑' : ($trend < 0 ? '↓' : '→') }}
                                </div>
                                <div class="text-xs text-gray-500">Tendência</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Script para estrelas interativas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = document.querySelectorAll('.star-label');
            const descriptions = {
                1: '😟 Ruim — Situação piorou significativamente',
                2: '😐 Regular — Pouca ou nenhuma evolução',
                3: '🙂 Bom — Evolução moderada',
                4: '😊 Muito Bom — Boa evolução',
                5: '🌟 Excelente — Grande melhora!'
            };

            labels.forEach(label => {
                label.addEventListener('click', function() {
                    const value = parseInt(this.dataset.value);
                    document.getElementById('star-description').textContent = descriptions[value];

                    // Update visual state
                    labels.forEach(l => {
                        const lv = parseInt(l.dataset.value);
                        l.classList.toggle('text-yellow-400', lv <= value);
                        l.classList.toggle('text-gray-300', lv > value);
                    });
                });

                label.addEventListener('mouseenter', function() {
                    const value = parseInt(this.dataset.value);
                    labels.forEach(l => {
                        const lv = parseInt(l.dataset.value);
                        l.classList.toggle('text-yellow-400', lv <= value);
                        l.classList.toggle('text-gray-300', lv > value);
                    });
                });
            });

            // Reset on mouse leave
            document.getElementById('star-rating').addEventListener('mouseleave', function() {
                const checked = document.querySelector('input[name="avaliacao"]:checked');
                const selectedValue = checked ? parseInt(checked.value) : 0;
                labels.forEach(l => {
                    const lv = parseInt(l.dataset.value);
                    l.classList.toggle('text-yellow-400', lv <= selectedValue);
                    l.classList.toggle('text-gray-300', lv > selectedValue);
                });
            });
        });
    </script>
</x-app-layout>
