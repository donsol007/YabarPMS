<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text"
               x-ref="input"
               x-on:focus="open = true"
               x-on:click.outside="open = false"
               x-on:keydown.escape.window="open = false; $el.blur()"
               wire:model.live.debounce.300ms="query"
               placeholder="Search clients…"
               class="w-44 sm:w-64 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 pl-9 pr-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500" />
    </div>

    <div x-show="open" x-cloak x-transition
         class="absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl z-50 overflow-hidden">
        @if (strlen(trim($this->query)) >= 2)
            @php($results = $this->results())
            @if ($results->isNotEmpty())
                <div class="max-h-80 overflow-y-auto py-1">
                    @foreach ($results as $client)
                        <button type="button"
                                wire:click="selectClient({{ $client->id }})"
                                wire:key="sr-{{ $client->id }}"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-xs font-bold">
                                {{ strtoupper(substr($client->first_name, 0, 1).substr($client->surname, 0, 1)) }}
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $client->full_name }}</span>
                                <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ $client->client_id }} · {{ $client->email }}
                                </span>
                            </span>
                            @if ($client->portfolio)
                                <span class="ml-auto shrink-0 text-xs text-slate-400">Portfolio ✓</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">No clients match “{{ $this->query }}”.</div>
            @endif
        @else
            <div class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">Type at least 2 characters to search by name, phone, email or account number.</div>
        @endif
    </div>
</div>