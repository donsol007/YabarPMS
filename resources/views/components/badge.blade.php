@props(['color' => 'gray'])

@php
    $colors = [
        'green' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
        'amber' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
        'red' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
        'gray' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300',
        'blue' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        'brand' => 'bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.($colors[$color] ?? $colors['gray'])]) }}>
    {{ $slot }}
</span>