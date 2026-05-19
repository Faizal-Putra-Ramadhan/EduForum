@props(['active', 'icon', 'open'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-600/20 font-bold transition-all transition-all duration-300 transform scale-[1.02]'
            : 'flex items-center px-4 py-3 text-gray-500 hover:text-emerald-400 hover:bg-emerald-500/5 rounded-xl font-bold transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    <span x-show="{{ $open }}" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 -translate-x-2"
          x-transition:enter-end="opacity-100 translate-x-0"
          class="ml-3 whitespace-nowrap overflow-hidden">
        {{ $slot }}
    </span>
</a>
