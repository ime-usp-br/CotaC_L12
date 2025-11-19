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
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    id="codpes"
                    wire:model="codpes"
                    autocomplete="off"
                    placeholder="{{ __('Digite o Número USP') }}"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    {{ $consumidorData ? 'disabled' : '' }}
                >
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ $consumidorData ? 'disabled' : '' }}
                >
                    {{ __('Buscar') }}
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
            @error('codpes')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </form>

    {{-- Mensagem de erro --}}
    @if($errorMessage)
        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
            <p class="text-sm text-red-800 dark:text-red-200">{{ $errorMessage }}</p>
        </div>
    @endif

    {{-- Dados do consumidor --}}
    @if($consumidorData && $saldoInfo)
        <div class="mt-6 space-y-4">
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
