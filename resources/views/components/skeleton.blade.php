{{-- Skeleton Loader Component --}}
@props([
    'type' => 'text', // text, card, avatar, button
    'lines' => 1,
    'height' => null,
    'width' => null,
])

@php
    $baseClasses = 'animate-pulse bg-gray-300 dark:bg-gray-700 rounded';
    
    $typeClasses = match($type) {
        'text' => 'h-4',
        'card' => 'h-32',
        'avatar' => 'h-12 w-12 rounded-full',
        'button' => 'h-10 w-24',
        default => 'h-4',
    };
    
    $heightClass = $height ? "h-{$height}" : '';
    $widthClass = $width ? "w-{$width}" : 'w-full';
@endphp

@if($type === 'text' && $lines > 1)
    {{-- Multiple text lines --}}
    <div class="space-y-3">
        @for($i = 0; $i < $lines; $i++)
            <div class="{{ $baseClasses }} {{ $typeClasses }} {{ $widthClass }} {{ $i === $lines - 1 ? 'w-3/4' : '' }}"></div>
        @endfor
    </div>
@else
    {{-- Single skeleton element --}}
    <div {{ $attributes->merge(['class' => "{$baseClasses} {$typeClasses} {$heightClass} {$widthClass}"]) }}></div>
@endif
