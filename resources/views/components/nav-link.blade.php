
@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'flex w-full items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-semibold
        bg-blue-500 text-white
        dark:bg-blue-500/10 dark:text-blue-400
        focus:outline-none focus-visible:ring-2
        focus-visible:ring-blue-500
        transition-colors duration-200'
        
        : 'flex w-full items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium
        text-gray-600 dark:text-gray-300
        hover:bg-gray-50 hover:text-gray-900
        dark:hover:bg-gray-800 dark:hover:text-white
        focus:outline-none focus-visible:ring-2
        focus-visible:ring-blue-500
        transition-colors duration-200';
@endphp

<a
    {{ $attributes->merge(['class' => $classes]) }}
    @if($active)
        aria-current="page"
    @endif
>
    {{ $slot }}
</a>
