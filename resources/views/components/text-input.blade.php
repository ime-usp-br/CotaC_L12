@props([
    'disabled' => false,
    'validatable' => false,
    'valid' => null,
])

@php
    $baseClasses = 'rounded-md shadow-sm transition-all duration-200';
    
    // Validation state classes
    if ($validatable && $valid !== null) {
        if ($valid) {
            // Valid state - green border
            $validationClasses = 'border-green-500 dark:border-green-600 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 pr-10';
        } else {
            // Invalid state - red border
            $validationClasses = 'border-red-500 dark:border-red-600 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 pr-10';
        }
    } else {
        // Default state
        $validationClasses = 'border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600';
    }
    
    $classes = "{$baseClasses} {$validationClasses} dark:bg-gray-900 dark:text-gray-300";
@endphp

<div class="relative">
    <input 
        @disabled($disabled) 
        {{ $attributes->merge(['class' => $classes]) }}
    >
    
    @if($validatable && $valid !== null)
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            @if($valid)
                {{-- Valid checkmark icon --}}
                <svg class="h-5 w-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            @else
                {{-- Invalid X icon --}}
                <svg class="h-5 w-5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            @endif
        </div>
    @endif
</div>
