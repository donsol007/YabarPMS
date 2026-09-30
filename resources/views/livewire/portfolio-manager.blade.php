<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $portfolio->client->full_name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $portfolio->client->client_id }} · {{ $portfolio->client->email ?? '' }} · Created {{ format_date($portfolio->created_at) }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit portfolios')
                @unless (request()->routeIs('portfolios.edit'))
                    <a href="{{ route('portfolios.edit', $portfolio) }}" class="btn-secondary" wire:navigate>Edit</a>
                @endunless
            @endcan
            @can('delete portfolios')
                <x-confirm-dialog
                    name="delete-portfolio"
                    title="Delete portfolio"
                    message="Delete this portfolio? Its instruments, history and equities will be hidden."
                    action="{{ route('portfolios.destroy', $portfolio) }}"
                />
            @endcan
            <a href="{{ route('reports.index') }}" class="btn-secondary" wire:navigate>Generate Report</a>
            @if ($portfolio->client->email)
                <form method="POST" action="{{ route('reports.portfolio.email') }}">
                    @csrf
                    <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}" />
                    <button type="submit" class="btn-secondary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email Report
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Summary card --}}
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <div class="card p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Fixed Debt</p>
            <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ format_money($portfolio->total_fixed_debt) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Equity Value</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ format_money($portfolio->total_equity_value) }}</p>
        </div>
        <div class="card p-5 bg-brand-50 dark:bg-brand-900/20 border-brand-200 dark:border-brand-800">
            <p class="text-xs font-medium uppercase tracking-wide text-brand-600 dark:text-brand-400">Total Portfolio Value</p>
            <p class="mt-1 text-2xl font-bold text-brand-700 dark:text-brand-300">{{ format_money($portfolio->total_portfolio_value) }}</p>
        </div>
    </div>

    {{-- Additional Information --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Additional Information</h3>
        </div>
        <div class="card-body">
            @if ($this->canMutate())
                <form wire:submit.prevent="saveAdditionalInformation">
                    <textarea wire:model="additionalInformation" rows="3"
                              class="input mt-1 w-full" placeholder="Any extra details to include on this portfolio's report…"></textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="saveAdditionalInformation">
                            <svg wire:loading wire:target="saveAdditionalInformation" x-cloak
                                 class="h-3.5 w-3.5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="saveAdditionalInformation">Save Additional Information</span>
                            <span wire:loading wire:target="saveAdditionalInformation">Saving…</span>
                        </button>
                    </div>
                </form>
            @elseif (filled($portfolio->additional_information))
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $portfolio->additional_information }}</p>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">No additional information provided.</p>
            @endif
        </div>
    </div>

    {{-- Fixed Debt Instruments --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Fixed Debt Instruments</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Fixed-income investments with payment breakdowns.</p>
            </div>
            @if ($this->canMutate())
                <button type="button" wire:click="openInstrumentCreate" class="btn-primary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Instrument
                </button>
            @endif
        </div>

        @if ($portfolio->fixedDebtInstruments->isEmpty())
            <div class="p-6">
                <x-empty-state title="No fixed debt instruments" description="Add a fixed debt instrument to this portfolio." />
            </div>
        @else
            <div class="table-scroll">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Description</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($portfolio->fixedDebtInstruments as $instrument)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $instrument->description }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_money($instrument->amount) }}</td>
                                <td class="px-5 py-3">
                                    <x-badge :color="strtolower($instrument->status) === 'active' ? 'green' : (strtolower($instrument->status) === 'matured' ? 'blue' : 'gray')">
                                        {{ $instrument->status }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" wire:click="openInstrumentView({{ $instrument->id }})" class="btn-ghost btn-sm">View</button>
                                        @if ($this->canMutate())
                                            <button type="button" wire:click="openInstrumentEdit({{ $instrument->id }})" class="btn-ghost btn-sm">Edit</button>
                                            <x-confirm-dialog
                                                name="delete-instrument-{{ $instrument->id }}"
                                                title="Delete instrument"
                                                message="Delete this instrument and its payment breakdown?"
                                                buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                wireClick="deleteInstrument({{ $instrument->id }})"
                                            />
                                        @endif
                                        <a href="{{ route('portfolios.instruments.print', [$portfolio, $instrument]) }}" target="_blank" class="btn-ghost btn-sm">Print</a>
                                        <a href="{{ route('portfolios.instruments.pdf', [$portfolio, $instrument]) }}" class="btn-ghost btn-sm">PDF</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Payment History --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Payment History</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Independent records of payments made against this portfolio.</p>
            </div>
            @if ($this->canMutate())
                <button type="button" wire:click="openHistoryCreate" class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Record
                </button>
            @endif
        </div>

        @if ($portfolio->paymentHistories->isEmpty())
            <div class="p-6">
                <x-empty-state title="No payment history" description="Records of payments made will appear here." />
            </div>
        @else
            <div class="table-scroll">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Amount Paid</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payment Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($portfolio->paymentHistories as $history)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_date($history->date) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-slate-800 dark:text-slate-100">{{ format_money($history->amount_paid) }}</td>
                                <td class="px-5 py-3">
                                    <x-badge :color="$history->payment_status === 'paid' ? 'green' : ($history->payment_status === 'unpaid' ? 'amber' : 'gray')">{{ $history->label }}</x-badge>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    @if ($this->canMutate())
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" wire:click="openHistoryEdit({{ $history->id }})" class="btn-ghost btn-sm">Edit</button>
                                            <x-confirm-dialog
                                                name="delete-history-{{ $history->id }}"
                                                title="Delete payment history"
                                                message="Delete this payment history record?"
                                                buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                wireClick="deleteHistory({{ $history->id }})"
                                            />
                                        </div>
                                    @else
                                        <x-badge color="gray">View only</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Equity --}}
    <div class="card mb-6">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Equity</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Equity holdings — value is computed as unit × price.</p>
            </div>
            @if ($this->canMutate())
                <button type="button" wire:click="openEquityCreate" class="btn-secondary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Equity
                </button>
            @endif
        </div>

        @if ($portfolio->equities->isEmpty())
            <div class="p-6">
                <x-empty-state title="No equity holdings" description="Equity positions will appear here." />
            </div>
        @else
            <div class="table-scroll">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Stock</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Unit</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Price</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Value</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($portfolio->equities as $equity)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $equity->stock }}</td>
                                <td class="px-5 py-3 text-right text-sm text-slate-600 dark:text-slate-300">{{ number_format((float) $equity->unit, 4) }}</td>
                                <td class="px-5 py-3 text-right text-sm text-slate-600 dark:text-slate-300">{{ format_money($equity->price) }}</td>
                                <td class="px-5 py-3 text-right text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ format_money($equity->value) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    @if ($this->canMutate())
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" wire:click="openEquityEdit({{ $equity->id }})" class="btn-ghost btn-sm">Edit</button>
                                            <x-confirm-dialog
                                                name="delete-equity-{{ $equity->id }}"
                                                title="Delete equity holding"
                                                message="Delete this equity holding?"
                                                buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                wireClick="deleteEquity({{ $equity->id }})"
                                            />
                                        </div>
                                    @else
                                        <x-badge color="gray">View only</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ================================================================== --}}
    {{--  Instrument modal (create / edit / view) with detail + breakdown  --}}
    {{-- ================================================================== --}}
    <div x-data="{ show: @entangle('showInstrumentModal') }"
         x-show="show"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[80] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/60" @click="show = false"></div>
        <div class="relative mx-auto mt-8 w-full max-w-3xl rounded-xl bg-white dark:bg-slate-800 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                    @if ($instrumentModalMode === 'create')
                        Add Fixed Debt Instrument
                    @elseif ($instrumentModalMode === 'view')
                        Instrument Details
                    @else
                        Edit Fixed Debt Instrument
                    @endif
                </h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="show = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 space-y-6 max-h-[70vh] overflow-y-auto">
                @php($readonly = $instrumentModalMode === 'view' || ! $this->canMutate())

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <x-input-label for="instrument-description" :value="__('Description')" />
                        <x-text-input id="instrument-description" wire:model="instrumentForm.description" class="mt-1 w-full" :disabled="$readonly" placeholder="e.g. Commercial Paper 2026" />
                        @error('instrumentForm.description') <x-input-error :messages="$errors->get('instrumentForm.description')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="instrument-amount" :value="__('Amount')" />
                        <x-text-input id="instrument-amount" type="number" step="0.01" min="0" wire:model="instrumentForm.amount" class="mt-1 w-full" :disabled="$readonly" placeholder="0.00" />
                        @error('instrumentForm.amount') <x-input-error :messages="$errors->get('instrumentForm.amount')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="instrument-status" :value="__('Status')" />
                        <x-select id="instrument-status" wire:model="instrumentForm.status" class="mt-1 w-full" :disabled="$readonly"
                                  :options="['Active' => 'Active', 'Matured' => 'Matured', 'Closed' => 'Closed']" />
                        @error('instrumentForm.status') <x-input-error :messages="$errors->get('instrumentForm.status')" class="mt-2" /> @enderror
                    </div>
                </div>

                {{-- Instrument details --}}
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-3">Instrument Details</h4>
                    <div class="grid gap-4 sm:grid-cols-5">
                        <div>
                            <x-input-label for="detail-subscription" :value="__('Subscription Date')" />
                            <x-text-input id="detail-subscription" type="date" wire:model="detailForm.subscription_date" class="mt-1 w-full" :disabled="$readonly" />
                            @error('detailForm.subscription_date') <x-input-error :messages="$errors->get('detailForm.subscription_date')" class="mt-2" /> @enderror
                        </div>
                        <div>
                            <x-input-label for="detail-instrument" :value="__('Instrument')" />
                            <x-text-input id="detail-instrument" wire:model="detailForm.instrument" class="mt-1 w-full" :disabled="$readonly" placeholder="CP" />
                            @error('detailForm.instrument') <x-input-error :messages="$errors->get('detailForm.instrument')" class="mt-2" /> @enderror
                        </div>
                        <div>
                            <x-input-label for="detail-tenor" :value="__('Tenor')" />
                            <x-text-input id="detail-tenor" wire:model="detailForm.tenor" class="mt-1 w-full" :disabled="$readonly" placeholder="e.g. 90 days" />
                            @error('detailForm.tenor') <x-input-error :messages="$errors->get('detailForm.tenor')" class="mt-2" /> @enderror
                        </div>
                        <div>
                            <x-input-label for="detail-rate" :value="__('Rental Rate')" />
                            <x-text-input id="detail-rate" type="number" step="0.01" min="0" wire:model="detailForm.rental_rate" class="mt-1 w-full" :disabled="$readonly" placeholder="0.00" />
                            @error('detailForm.rental_rate') <x-input-error :messages="$errors->get('detailForm.rental_rate')" class="mt-2" /> @enderror
                        </div>
                        <div>
                            <x-input-label for="detail-settlement" :value="__('Settlement Date')" />
                            <x-text-input id="detail-settlement" type="date" wire:model="detailForm.settlement_date" class="mt-1 w-full" :disabled="$readonly" />
                            @error('detailForm.settlement_date') <x-input-error :messages="$errors->get('detailForm.settlement_date')" class="mt-2" /> @enderror
                        </div>
                    </div>
                </div>

                {{-- Payment breakdown --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payment Breakdown</h4>
                        @if ($this->canMutate() && $editingInstrumentId)
                            <button type="button" wire:click="openBreakdownCreate({{ $editingInstrumentId }})" class="btn-primary btn-sm">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Breakdown
                            </button>
                        @endif
                    </div>

                    @php($breakdowns = $editingInstrumentId
                        ? $portfolio->fixedDebtInstruments->firstWhere('id', $editingInstrumentId)?->paymentBreakdowns ?? collect()
                        : collect())
                    @if ($breakdowns->isEmpty())
                        <p class="rounded-lg border border-dashed border-slate-200 dark:border-slate-700 px-4 py-5 text-center text-sm text-slate-500 dark:text-slate-400">
                            No payment breakdowns for this instrument yet.
                        </p>
                    @else
                        <div class="table-scroll rounded-lg border border-slate-200 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-800/60">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">ROI</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Amount</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Payment Date</th>
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach ($breakdowns as $breakdown)
                                        <tr>
                                            <td class="px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ $breakdown->roi }}</td>
                                            <td class="px-4 py-2.5 text-sm font-medium text-slate-800 dark:text-slate-100">{{ format_money($breakdown->amount) }}</td>
                                            <td class="px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300">{{ format_date($breakdown->payment_date) }}</td>
                                            <td class="px-4 py-2.5"><x-badge :color="$breakdown->badge_color">{{ $breakdown->label }}</x-badge></td>
                                            <td class="px-4 py-2.5 whitespace-nowrap text-right">
                                                @if ($this->canMutate())
                                                    <div class="flex items-center justify-end gap-1">
                                                        <button type="button" wire:click="openBreakdownEdit({{ $breakdown->id }})" class="btn-ghost btn-sm">Edit</button>
                                                        <x-confirm-dialog
                                                            name="delete-breakdown-{{ $breakdown->id }}"
                                                            title="Delete payment breakdown"
                                                            message="Delete this payment breakdown?"
                                                            buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                            wireClick="deleteBreakdown({{ $breakdown->id }})"
                                                        />
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                <button type="button" class="btn-secondary" @click="show = false">Close</button>
                @if (! $readonly)
                    <button type="button" wire:click="saveInstrument" class="btn-primary">
                        <svg wire:loading wire:target="saveInstrument" class="h-3.5 w-3.5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span wire:loading.remove wire:target="saveInstrument">Save Instrument</span>
                        <span wire:loading wire:target="saveInstrument">Saving…</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Breakdown modal --}}
    <div x-data="{ show: @entangle('showBreakdownModal') }"
         x-show="show"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[90] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/60" @click="show = false"></div>
        <div class="relative mx-auto mt-16 w-full max-w-lg rounded-xl bg-white dark:bg-slate-800 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $editingBreakdownId ? 'Edit Payment Breakdown' : 'Add Payment Breakdown' }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="show = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="bd-roi" :value="__('ROI')" />
                        <x-text-input id="bd-roi" type="text" wire:model="breakdownForm.roi" class="mt-1 w-full" placeholder="ROI 1" />
                        @error('breakdownForm.roi') <x-input-error :messages="$errors->get('breakdownForm.roi')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="bd-amount" :value="__('Amount')" />
                        <x-text-input id="bd-amount" type="number" step="0.01" min="0" wire:model="breakdownForm.amount" class="mt-1 w-full" placeholder="0.00" />
                        @error('breakdownForm.amount') <x-input-error :messages="$errors->get('breakdownForm.amount')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="bd-date" :value="__('Payment Date')" />
                        <x-text-input id="bd-date" type="date" wire:model="breakdownForm.payment_date" class="mt-1 w-full" />
                        @error('breakdownForm.payment_date') <x-input-error :messages="$errors->get('breakdownForm.payment_date')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="bd-status" :value="__('Status')" />
                        <x-select id="bd-status" wire:model="breakdownForm.status" class="mt-1 w-full"
                                  :options="['paid' => 'Paid', 'unpaid' => 'Unpaid (Pending)', 'blank' => 'Blank (Not due)']" />
                        @error('breakdownForm.status') <x-input-error :messages="$errors->get('breakdownForm.status')" class="mt-2" /> @enderror
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                <button type="button" class="btn-secondary" @click="show = false">Cancel</button>
                <button type="button" wire:click="saveBreakdown" class="btn-primary">Save</button>
            </div>
        </div>
    </div>

    {{-- History modal --}}
    <div x-data="{ show: @entangle('showHistoryModal') }"
         x-show="show"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[90] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/60" @click="show = false"></div>
        <div class="relative mx-auto mt-16 w-full max-w-lg rounded-xl bg-white dark:bg-slate-800 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $editingHistoryId ? 'Edit Payment History' : 'Add Payment History' }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="show = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="ph-date" :value="__('Date')" />
                        <x-text-input id="ph-date" type="date" wire:model="historyForm.date" class="mt-1 w-full" />
                        @error('historyForm.date') <x-input-error :messages="$errors->get('historyForm.date')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="ph-amount" :value="__('Amount Paid')" />
                        <x-text-input id="ph-amount" type="number" step="0.01" min="0" wire:model="historyForm.amount_paid" class="mt-1 w-full" placeholder="0.00" />
                        @error('historyForm.amount_paid') <x-input-error :messages="$errors->get('historyForm.amount_paid')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="ph-status" :value="__('Payment Status')" />
                        <x-select id="ph-status" wire:model="historyForm.payment_status" class="mt-1 w-full"
                                  :options="['paid' => 'Paid', 'unpaid' => 'Unpaid (Pending)', 'blank' => 'Blank (Not due)']" />
                        @error('historyForm.payment_status') <x-input-error :messages="$errors->get('historyForm.payment_status')" class="mt-2" /> @enderror
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                <button type="button" class="btn-secondary" @click="show = false">Cancel</button>
                <button type="button" wire:click="saveHistory" class="btn-primary">Save</button>
            </div>
        </div>
    </div>

    {{-- Equity modal --}}
    <div x-data="{ show: @entangle('showEquityModal') }"
         x-show="show"
         x-cloak
         x-transition:opacity
         class="fixed inset-0 z-[90] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/60" @click="show = false"></div>
        <div class="relative mx-auto mt-16 w-full max-w-lg rounded-xl bg-white dark:bg-slate-800 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $editingEquityId ? 'Edit Equity' : 'Add Equity' }}</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600" @click="show = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-3">
                        <x-input-label for="eq-stock" :value="__('Stock')" />
                        <x-text-input id="eq-stock" wire:model="equityForm.stock" class="mt-1 w-full" placeholder="e.g. GUARANTY" />
                        @error('equityForm.stock') <x-input-error :messages="$errors->get('equityForm.stock')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="eq-unit" :value="__('Unit')" />
                        <x-text-input id="eq-unit" type="number" step="0.0001" min="0" wire:model="equityForm.unit" class="mt-1 w-full" placeholder="0" />
                        @error('equityForm.unit') <x-input-error :messages="$errors->get('equityForm.unit')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="eq-price" :value="__('Price')" />
                        <x-text-input id="eq-price" type="number" step="0.01" min="0" wire:model="equityForm.price" class="mt-1 w-full" placeholder="0.00" />
                        @error('equityForm.price') <x-input-error :messages="$errors->get('equityForm.price')" class="mt-2" /> @enderror
                    </div>
                    <div>
                        <x-input-label for="eq-value" :value="__('Value')" />
                        <div class="mt-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ format_money(((float) ($equityForm['unit'] ?? 0)) * ((float) ($equityForm['price'] ?? 0))) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                <button type="button" class="btn-secondary" @click="show = false">Cancel</button>
                <button type="button" wire:click="saveEquity" class="btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>