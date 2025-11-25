<div wire:poll.5s>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($pedidos as $pedido)
            <div wire:key="pedido-{{ $pedido->id }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-500 ease-in-out animate-slide-in-up hover:-translate-y-1 hover:shadow-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ __('Pedido #') }}{{ $pedido->id }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $pedido->created_at->format('H:i:s') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $pedido->consumidor->nome ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $pedido->consumidor->codpes ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 py-4">
                        <ul class="space-y-2">
                            @foreach ($pedido->itens as $item)
                                <li class="flex justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $item->quantidade }}x {{ $item->produto->nome ?? 'Produto Removido' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-4">
                        <button
                            wire:click="marcarComoEntregue({{ $pedido->id }})"
                            wire:loading.attr="disabled"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all ease-in-out duration-150"
                        >
                            <span wire:loading.remove wire:target="marcarComoEntregue({{ $pedido->id }})">
                                {{ __('Marcar como Entregue') }}
                            </span>
                            <span wire:loading wire:target="marcarComoEntregue({{ $pedido->id }})" class="flex items-center gap-2">
                                <x-spinner size="sm" color="white" />
                                {{ __('Processando...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 animate-fade-in">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Nenhum pedido pendente') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Novos pedidos aparecerão aqui automaticamente.') }}</p>
            </div>
        @endforelse
    </div>
</div>
