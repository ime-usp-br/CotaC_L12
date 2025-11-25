{{-- Spinner Component --}}
@props([
    'size' =\u003e 'md', // sm, md, lg, xl
    'color' =\u003e 'blue', // blue, white, gray, green, red
])

@php
    $sizeClasses = match($size) {
        'sm' =\u003e 'h-4 w-4',
        'md' =\u003e 'h-6 w-6',
        'lg' =\u003e 'h-8 w-8',
        'xl' =\u003e 'h-12 w-12',
        default =\u003e 'h-6 w-6',
    };
    
    $colorClasses = match($color) {
        'blue' =\u003e 'text-blue-600',
        'white' =\u003e 'text-white',
        'gray' =\u003e 'text-gray-600',
        'green' =\u003e 'text-green-600',
        'red' =\u003e 'text-red-600',
        default =\u003e 'text-blue-600',
    };
@endphp

\u003csvg 
    {{ $attributes-\u003emerge(['class' =\u003e "animate-spin {$sizeClasses} {$colorClasses}"]) }}
    xmlns=\"http://www.w3.org/2000/svg\" 
    fill=\"none\" 
    viewBox=\"0 0 24 24\"
    role=\"status\"
    aria-label=\"{{ __('Loading...') }}\"
\u003e
    \u003ccircle 
        class=\"opacity-25\" 
        cx=\"12\" 
        cy=\"12\" 
        r=\"10\" 
        stroke=\"currentColor\" 
        stroke-width=\"4\"
    \u003e\u003c/circle\u003e
    \u003cpath 
        class=\"opacity-75\" 
        fill=\"currentColor\" 
        d=\"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\"
    \u003e\u003c/path\u003e
\u003c/svg\u003e
