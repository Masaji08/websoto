@props([
    'title' => '',
    'value' => '',
    'change' => '',
    'trend' => 'up',
    'icon' => '',
    'color' => 'primary',
])

@php
    $colors = [
        'primary' => 'bg-[#FF8C42]',
        'accent' => 'bg-[#6D4C41]',
        'green' => 'bg-emerald-500',
        'blue' => 'bg-blue-500',
    ];
    $bgColors = [
        'primary' => 'bg-[#FF8C42]/10',
        'accent' => 'bg-[#6D4C41]/20',
        'green' => 'bg-emerald-50',
        'blue' => 'bg-blue-50',
    ];
    $iconColor = [
        'primary' => 'text-[#FF8C42]',
        'accent' => 'text-[#6D4C41]',
        'green' => 'text-emerald-600',
        'blue' => 'text-blue-600',
    ];
@endphp

<div class="bg-white rounded-xl border border-gray-100 p-4 md:p-5 shadow-sm hover-card">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <p class="text-xs md:text-sm text-gray-500 font-medium">{{ $title }}</p>
            <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if ($change)
                <p class="text-xs flex items-center gap-1 {{ $trend === 'up' ? 'text-emerald-600' : 'text-red-500' }}">
                    @if ($trend === 'up')
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @endif
                    {{ $change }}
                </p>
            @endif
        </div>
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg {{ $bgColors[$color] }} flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 md:w-6 md:h-6 {{ $iconColor[$color] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
            </svg>
        </div>
    </div>
</div>
