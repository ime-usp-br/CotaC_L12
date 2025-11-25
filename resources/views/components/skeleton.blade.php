{{-- Skeleton Loader Component --}}
@props([
    'type' =\u003e 'text', // text, card, avatar, button
    'lines' =\u003e 1,
    'height' =\u003e null,
    'width' =\u003e null,
])

@php
    $baseClasses = 'animate-pulse bg-gray-300 dark:bg-gray-700 rounded';
    
    $typeClasses = match($type) {
        'text' =\u003e 'h-4',
        'card' =\u003e 'h-32',
        'avatar' =\u003e 'h-12 w-12 rounded-full',
        'button' =\u003e 'h-10 w-24',
        default =\u003e 'h-4',
    };
    
    $heightClass = $height ? "h-{$height}" : '';
    $widthClass = $width ? "w-{$width}" : 'w-full';
@endphp

@if($type === 'text' && $lines \u003e 1)
    {{-- Multiple text lines --}}
    \u003cdiv class=\"space-y-3\"\u003e
        @for($i = 0; $i \u003c $lines; $i++)
            \u003cdiv class=\"{{ $baseClasses }} {{ $typeClasses }} {{ $widthClass }} {{ $i === $lines - 1 ? 'w-3/4' : '' }}\"\u003e\u003c/div\u003e
        @endfor
    \u003c/div\u003e
@else
    {{-- Single skeleton element --}}
    \u003cdiv {{ $attributes-\u003emerge(['class' =\u003e "{$baseClasses} {$typeClasses} {$heightClass} {$widthClass}"]) }}\u003e\u003c/div\u003e
@endif
