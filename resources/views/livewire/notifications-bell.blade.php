<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">
    <button type="button" @click="open = !open" class="relative rounded-lg p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Notifications">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-80 sm:w-96 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Notifications</h3>
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllRead" class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @php($items = $this->recent())
            @forelse ($items as $notification)
                @php($data = $notification->data)
                <button type="button"
                        wire:click="markAsRead('{{ $notification->id }}')"
                        wire:key="n-{{ $notification->id }}"
                        class="flex w-full items-start gap-3 px-4 py-3 text-left border-b border-slate-50 dark:border-slate-700/60 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                    <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-slate-100 dark:bg-slate-700 text-slate-400' : 'bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-300' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium {{ $notification->read_at ? 'text-slate-600 dark:text-slate-300' : 'text-slate-800 dark:text-slate-100' }}">{{ $data['title'] ?? 'Notification' }}</span>
                        <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $data['body'] ?? '' }}</span>
                        <span class="mt-1 block text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                    @if (! $notification->read_at)
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                    @endif
                </button>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">You're all caught up.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>