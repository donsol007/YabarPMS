@props(['rows' => 4, 'cols' => 4])

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @for ($i = 0; $i < $rows; $i++)
        <div class="grid gap-3" style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr));">
            @for ($j = 0; $j < $cols; $j++)
                <div class="h-4 rounded bg-slate-200/70 dark:bg-slate-800 animate-pulse"></div>
            @endfor
        </div>
    @endfor
</div>