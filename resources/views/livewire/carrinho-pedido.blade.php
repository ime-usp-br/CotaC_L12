<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 sticky top-4">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        {{ __('Carrinho') }}
    </h2>

    {{-- Itens do carrinho --}}
    @if(empty($itens))
        <div class="py-8 text-center animate-fade-in">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Carrinho vazio') }}
            </p>
        </div>
    @else
        <div class="space-y-3 mb-4">
            @foreach($produtos as $produto)
                @php
                    $quantidade = $itens[$produto->id] ?? 0;
                    $subtotal = $produto->valor * $quantidade;
                @endphp
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md animate-slide-in-left">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $produto->nome }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $quantidade }}x {{ $produto->valor }} = {{ $subtotal }}
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="removerProduto({{ $produto->id }})"
                        class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors active:scale-95"
                        title="{{ __('Remover') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="font-semibold text-gray-900 dark:text-white">
                    {{ __('Total:') }}
                </span>
                <span class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $total }}
                </span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-400">
                    {{ __('Saldo disponível:') }}
                </span>
                <span class="{{ $saldoSuficiente ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                    {{ $saldoDisponivel }}
                </span>
            </div>
        </div>

        {{-- Aviso de saldo insuficiente --}}
        @if(!$saldoSuficiente)
            <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                <p class="text-sm text-red-800 dark:text-red-200">
                    {{ __('Saldo insuficiente! Remova alguns produtos.') }}
                </p>
            </div>
        @endif

        {{-- Botão finalizar --}}
        <button
            type="button"
            wire:click="finalizarPedido"
            wire:loading.attr="disabled"
            class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 flex items-center justify-center gap-2"
            {{ (!$saldoSuficiente || $processando) ? 'disabled' : '' }}
        >
            <span wire:loading.remove wire:target="finalizarPedido">
                {{ __('Finalizar Pedido') }}
            </span>
            <span wire:loading wire:target="finalizarPedido" class="flex items-center gap-2">
                <x-spinner size="sm" color="white" />
                {{ __('Processando...') }}
            </span>
        </button>
    @endif
</div>
