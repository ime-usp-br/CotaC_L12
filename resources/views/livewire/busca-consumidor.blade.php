<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        {{ __('Buscar Consumidor') }}
    </h2>

    {{-- Formulário de busca --}}
    <form wire:submit="buscar" class="space-y-4">
        <div>
            <label for="codpes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Número USP (NUSP)') }}
            </label>
            <div class="mt-1 flex gap-2">
                <x-text-input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    id="codpes"
                    wire:model.live="codpes"
                    wire:blur="validateNusp"
                    autocomplete="off"
                    placeholder="{{ __('Digite o Número USP') }}"
                    class="flex-1"
                    :disabled="$consumidorData !== null"
                    :validatable="true"
                    :valid="$nuspValid"
                />
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 flex items-center gap-2"
                    {{ $consumidorData ? 'disabled' : '' }}
                >
                    <span wire:loading.remove wire:target="buscar">
                        {{ __('Buscar') }}
                    </span>
                    <span wire:loading wire:target="buscar" class="flex items-center gap-2">
                        <x-spinner size="sm" color="white" />
                        {{ __('Buscando...') }}
                    </span>
                </button>
                @if($consumidorData)
                    <button
                        type="button"
                        wire:click="limpar"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        {{ __('Limpar') }}
                    </button>
                @endif
            </div>
            <x-input-error :messages="$errors->get('codpes')" />
        </div>
    </form>

    {{-- Loading skeleton while fetching data --}}
    <div wire:loading wire:target="buscar" class="mt-6 space-y-4">
        <x-skeleton type="card" />
        <div class="grid grid-cols-3 gap-4">
            <x-skeleton type="card" />
            <x-skeleton type="card" />
            <x-skeleton type="card" />
        </div>
    </div>

    {{-- Dados do consumidor --}}
    @if($consumidorData && $saldoInfo)
        <div class="mt-6 space-y-4 animate-fade-in" wire:loading.remove wire:target="buscar">
            <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">
                    {{ $consumidorData['nompes'] }}
                </h3>
                <p class="text-sm text-green-700 dark:text-green-300">
                    {{ __('N° USP:') }} {{ $consumidorData['codpes'] }}
                </p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                        {{ __('Cota Mensal') }}
                    </p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                        {{ $saldoInfo['cota'] }}
                    </p>
                </div>

                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">
                        {{ __('Gasto no Mês') }}
                    </p>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">
                        {{ $saldoInfo['gasto'] }}
                    </p>
                </div>

                <div class="p-4 {{ $saldoInfo['saldo'] > 0 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' }} border rounded-md">
                    <p class="text-sm {{ $saldoInfo['saldo'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ __('Saldo Disponível') }}
                    </p>
                    <p class="text-2xl font-bold {{ $saldoInfo['saldo'] > 0 ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100' }}">
                        {{ $saldoInfo['saldo'] }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
