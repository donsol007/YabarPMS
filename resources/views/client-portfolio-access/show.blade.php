<x-public-layout>
    <x-slot name="title">Portfolio — {{ $client->full_name }}</x-slot>

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">Portfolio Statement</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $client->full_name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $client->client_id }} ·
                Generated {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($portfolio)
                <a href="{{ route('portfolio.access.pdf', $token) }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </a>
            @endif
            <form method="POST" action="{{ route('portfolio.access.lock', $token) }}">
                @csrf
                <button type="submit" class="btn-ghost">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                    Exit
                </button>
            </form>
        </div>
    </div>

    @if (! $portfolio)
        <x-empty-state
            title="No portfolio on record"
            description="A portfolio has not been created for this account yet. Please contact us if you believe this is an error."
        />
    @else
        @php
            $totalFixedDebt = $portfolio->total_fixed_debt;
            $totalEquity = $portfolio->total_equity_value;
            $totalValue = $portfolio->total_portfolio_value;
        @endphp

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Fixed Debt</p>
                    <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ format_money($totalFixedDebt) }}</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Equity Value</p>
                    <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ format_money($totalEquity) }}</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Portfolio Value</p>
                    <p class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">{{ format_money($totalValue) }}</p>
                </div>
            </div>
        </div>

        <x-card title="Fixed Debt Instruments" description="Your subscriptions and their scheduled returns." class="mb-6">
            @forelse ($portfolio->fixedDebtInstruments as $instrument)
                <div class="mb-6 last:mb-0 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $instrument->description }}</h3>
                        <div class="flex items-center gap-2">
                            <x-badge color="brand">{{ format_money($instrument->amount) }}</x-badge>
                            <x-badge color="{{ $instrument->status === 'Active' ? 'green' : 'gray' }}">{{ $instrument->status }}</x-badge>
                        </div>
                    </div>

                    @if ($instrument->detail)
                        <dl class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Subscription Date</dt>
                                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ format_date($instrument->detail->subscription_date) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Instrument</dt>
                                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $instrument->detail->instrument ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Tenor</dt>
                                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $instrument->detail->tenor ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Rental Rate</dt>
                                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $instrument->detail->rental_rate !== null ? $instrument->detail->rental_rate.'%' : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Settlement Date</dt>
                                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ format_date($instrument->detail->settlement_date) }}</dd>
                            </div>
                        </dl>
                    @endif

                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payment Breakdown</p>
                    <div class="table-scroll">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    <th class="px-3 py-2 font-medium">ROI</th>
                                    <th class="px-3 py-2 font-medium">Amount</th>
                                    <th class="px-3 py-2 font-medium">Payment Date</th>
                                    <th class="px-3 py-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($instrument->paymentBreakdowns as $breakdown)
                                    <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                                        <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ $breakdown->roi }}</td>
                                        <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_money($breakdown->amount) }}</td>
                                        <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_date($breakdown->payment_date) }}</td>
                                        <td class="px-3 py-2"><x-badge :color="$breakdown->badge_color">{{ $breakdown->label }}</x-badge></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-3 py-4 text-center text-sm text-slate-400">No payment breakdowns.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No fixed debt instruments.</p>
            @endforelse
        </x-card>

        <x-card title="Payment History" class="mb-6">
            <div class="table-scroll">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <th class="px-3 py-2 font-medium">Date</th>
                            <th class="px-3 py-2 font-medium">Amount Paid</th>
                            <th class="px-3 py-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($portfolio->paymentHistories as $history)
                            <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_date($history->date) }}</td>
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_money($history->amount_paid) }}</td>
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ $history->label }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-4 text-center text-sm text-slate-400">No payment history.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Equity Holdings" class="mb-6">
            <div class="table-scroll">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <th class="px-3 py-2 font-medium">Stock</th>
                            <th class="px-3 py-2 font-medium">Unit</th>
                            <th class="px-3 py-2 font-medium">Price</th>
                            <th class="px-3 py-2 font-medium">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($portfolio->equities as $equity)
                            <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ $equity->stock }}</td>
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ number_format((float) $equity->unit, 4) }}</td>
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_money($equity->price) }}</td>
                                <td class="px-3 py-2 text-slate-800 dark:text-slate-100">{{ format_money($equity->value) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-sm text-slate-400">No equity holdings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        @if (filled($portfolio->additional_information))
            <x-card title="Additional Information" class="mb-6">
                <p class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-300">{{ $portfolio->additional_information }}</p>
            </x-card>
        @endif
    @endif
</x-public-layout>