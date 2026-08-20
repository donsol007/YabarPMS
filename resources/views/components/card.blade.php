@props(['title' => null, 'description' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $actions)
        <div class="card-header">
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $description }}</p>
                @endif
            </div>
            @if ($actions)
                <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div class="card-body">{{ $slot }}</div>
</div>