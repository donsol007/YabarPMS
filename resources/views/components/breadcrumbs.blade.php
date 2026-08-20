@props(['items' => [], 'home' => 'Dashboard'])

<nav {{ $attributes->merge(['class' => 'min-w-0 items-center gap-1 text-sm text-slate-500 dark:text-slate-400']) }} aria-label="Breadcrumb">
    <ol class="flex items-center gap-1.5 min-w-0">
        <li>
            <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-slate-700 dark:hover:text-slate-200">Home</a>
        </li>
        <li class="text-slate-300 dark:text-slate-600">/</li>
        <li>
            <a href="{{ route('dashboard') }}" wire:navigate class="font-medium text-slate-700 dark:text-slate-200">{{ $home }}</a>
        </li>
        @foreach ($items as $item)
            <li class="text-slate-300 dark:text-slate-600">/</li>
            <li class="min-w-0">
                @if (isset($item['url']))
                    <a href="{{ $item['url'] }}" wire:navigate class="hover:text-slate-700 dark:hover:text-slate-200 truncate">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-slate-700 dark:text-slate-200 truncate">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>