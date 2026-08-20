<x-app-layout>
    <x-slot name="title">Reports</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reports</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Generate and download PDF reports for portfolios and clients.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Portfolio report --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Portfolio Report</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Full portfolio summary, instruments, breakdowns, history and equity.</p>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.portfolio') }}" id="portfolio-report-form">
                    @csrf
                    <x-input-label for="portfolio_id" :value="__('Select Portfolio')" />
                    <select id="portfolio_id" name="portfolio_id" class="input mt-1 w-full" required>
                        <option value="">Select a portfolio…</option>
                        @foreach ($portfolios as $portfolio)
                            <option value="{{ $portfolio->id }}">{{ $portfolio->client?->full_name }} ({{ $portfolio->client?->client_id }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('portfolio_id')" class="mt-2" />

                    @if ($portfolios->isEmpty())
                        <p class="mt-3 rounded-lg bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm text-slate-500 dark:text-slate-400">No portfolios available to report on.</p>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="btn-primary" {{ $portfolios->isEmpty() ? 'disabled' : '' }}>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Generate &amp; Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.portfolio.email') }}" class="btn-secondary" {{ $portfolios->isEmpty() ? 'disabled' : '' }}>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Send to Client
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Client report --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Client Report</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Columnar PDF of clients with your chosen fields.</p>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.client') }}">
                    @csrf

                    <x-input-label :value="__('Include Fields')" />
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-56 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 p-3">
                        @foreach (\App\Models\Client::FIELD_LABELS as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="fields[]" value="{{ $key }}" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('fields')" class="mt-2" />

                    <x-input-label class="mt-4" :value="__('Select Clients')" />
                    <div class="mt-2 max-h-56 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 p-3 space-y-1">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <input type="checkbox" id="select-all-clients" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" x-data x-on:change="document.querySelectorAll('input[name=\'client_ids[]\']').forEach(el => el.checked = $el.checked)">
                            Select all
                        </label>
                        @foreach ($clients as $client)
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="client_ids[]" value="{{ $client->id }}" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                {{ $client->full_name }} ({{ $client->client_id }})
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('client_ids')" class="mt-2" />

                    @if ($clients->isEmpty())
                        <p class="mt-3 rounded-lg bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm text-slate-500 dark:text-slate-400">No clients available to report on.</p>
                    @endif

                    <button type="submit" class="btn-primary mt-4" {{ $clients->isEmpty() ? 'disabled' : '' }}>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Generate &amp; Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>