<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cabeçalho --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ __('Realizar Pedido') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Sistema de pedidos da cafeteria - Balcão') }}
                </p>
            </div>

            {{-- Mensagem de sucesso global --}}
            <div 
                x-data="{ 
                    show: false, 
                    message: '',
                    pedidoId: null
                }" 
                x-on:pedido-criado.window="
                    pedidoId = $event.detail.pedidoId;
                    message = '{{ __("Pedido #") }}' + pedidoId + '{{ __(" criado com sucesso!") }}';
                    show = true;
                    setTimeout(() => show = false, 5000);
                "
                x-show="show"
                x-transition
                class="mb-6"
                style="display: none;"
            >
                <div class="p-6 bg-green-50 dark:bg-green-900/20 border-2 border-green-500 dark:border-green-600 rounded-lg text-center">
                    <svg class="mx-auto h-12 w-12 text-green-600 dark:text-green-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-bold text-green-800 dark:text-green-200" x-text="message"></p>
                </div>
            </div>

            {{-- Busca de Consumidor --}}
            <div class="mb-6">
                <livewire:busca-consumidor />
            </div>

            {{-- Produtos e Carrinho --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Produtos (2/3 no desktop) --}}
                <div class="lg:col-span-2">
                    <livewire:selecao-produtos />
                </div>

                {{-- Carrinho (1/3 no desktop) --}}
                <div class="lg:col-span-1">
                    <livewire:carrinho-pedido />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
