@php($accessUrl = $client->portfolioAccessUrl())

<div class="mt-5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4" x-data="{ copied: false }">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client Portfolio Link</p>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Share this link and the access code. The code is required before the portfolio can be opened.</p>
        </div>
        <x-badge color="green">Access enabled</x-badge>
    </div>

    @if ($accessUrl)
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <input type="text" readonly value="{{ $accessUrl }}"
                   x-ref="link"
                   class="input flex-1 min-w-0 bg-white dark:bg-slate-900 text-xs font-mono"
                   onfocus="this.select()">

            <button type="button"
                    class="btn-secondary shrink-0"
                    @click="navigator.clipboard.writeText($refs.link.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                <svg x-show="!copied" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <svg x-show="copied" x-cloak class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="copied ? 'Copied' : 'Copy link'">Copy link</span>
            </button>

            @if ($client->email)
                <form method="POST" action="{{ route('clients.portfolio-access.email', $client) }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email link
                    </button>
                </form>
            @endif
        </div>

        <form method="POST" action="{{ route('clients.portfolio-access.regenerate', $client) }}" class="mt-3"
              onsubmit="return confirm('Regenerate the link? The previous link will stop working immediately.')">
            @csrf
            <button type="submit" class="btn-ghost btn-sm text-red-600 dark:text-red-400">
                Regenerate link
            </button>
        </form>
    @else
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Save an access code to generate the client link.</p>
    @endif
</div>