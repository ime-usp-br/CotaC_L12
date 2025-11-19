<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        {{ __('Produtos Disponíveis') }}
    </h2>

    @if(!$habilitado)
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Busque um consumidor para começar a fazer um pedido.') }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($produtos as $produto)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow {{ !$habilitado ? 'opacity-50' : '' }}">
                    <div class="flex flex-col h-full">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ $produto->nome }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Valor:') }}
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $produto->valor }}
                                </span>
                            </p>
                        </div>
                        <button
                            type="button"
                            wire:click="adicionarAoCarrinho({{ $produto->id }})"
                            class="mt-4 w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            {{ !$habilitado ? 'disabled' : '' }}
                        >
                            {{ __('Adicionar') }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('Nenhum produto disponível.') }}
                </div>
            @endforelse
        </div>
    @endif
</div>
