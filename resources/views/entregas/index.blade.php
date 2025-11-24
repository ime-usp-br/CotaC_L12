<x-delivery-layout>
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Pedidos Pendentes') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Acompanhe os pedidos prontos para entrega.') }}
            </p>
        </div>
        <div class="text-right">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('Atualização automática') }}
            </div>
            <div class="flex items-center justify-end space-x-2 text-green-600 dark:text-green-400">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs uppercase tracking-wider">{{ __('Ao Vivo') }}</span>
            </div>
        </div>
    </div>

    <livewire:lista-pedidos-pendentes />
</x-delivery-layout>
