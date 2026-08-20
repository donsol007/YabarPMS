@props(['label', 'value', 'icon' => null, 'accent' => 'brand', 'hint' => null, 'trend' => null])

@php
    $accents = [
        'brand' => 'bg-brand-50 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300',
        'green' => 'bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300',
        'amber' => 'bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300',
        'violet' => 'bg-violet-50 dark:bg-violet-900/40 text-violet-600 dark:text-violet-300',
        'sky' => 'bg-sky-50 dark:bg-sky-900/40 text-sky-600 dark:text-sky-300',
        'rose' => 'bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-300',
    ];
    $trendColors = [
        'up' => 'text-emerald-600 dark:text-emerald-400',
        'down' => 'text-red-600 dark:text-red-400',
        'flat' => 'text-slate-500 dark:text-slate-400',
    ];
    $icons = [
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
        'cash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l6-6 4 4 8-8m0 0v5m0-5h-5" />',
        'trending' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
    ];
    $svg = $icons[$icon] ?? $icons['cash'];
@endphp

<div class="card">
    <div class="card-body px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $accents[$accent] }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $svg !!}</svg>
            </div>
            <div class="min-w-0">
                <h3 class="truncate text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</h3>
                <p class="mt-0.5 truncate text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $value }}</p>
            </div>
        </div>
    </div>
    @if ($hint || $trend)
        <div class="card-footer flex items-center justify-between gap-2 border-t border-slate-100 dark:border-slate-800 px-5 py-2.5">
            <span class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $hint }}</span>
            @if ($trend)
                <span class="flex shrink-0 items-center gap-1 text-xs font-semibold {{ $trend['color'] }}">
                    @if (($trend['direction'] ?? 'up') === 'down') ↓ @else ↑ @endif
                    {{ $trend['label'] }}
                </span>
            @endif
        </div>
    @endif
</div>