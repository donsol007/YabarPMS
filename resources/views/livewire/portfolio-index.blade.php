@php
    $portfolios = $this->portfolios;
    $sortIcon = fn ($field) => $this->sortField === $field
        ? ($this->sortDirection === 'asc' ? '↑' : '↓')
        : '';
@endphp

<div>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">All client portfolios</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $portfolios->total() }} portfolios</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search portfolios…"
                           class="w-56 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 pl-9 pr-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500" />
                </div>
                @can('create portfolios')
                    <a href="{{ route('portfolios.create') }}" class="btn-primary" wire:navigate>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Portfolio
                    </a>
                @endcan
            </div>
        </div>

        <div class="overflow-x-auto">
            @if ($portfolios->isEmpty())
                <div class="p-6">
                    <x-empty-state
                        :title="$search ? 'No portfolios match your search' : 'No portfolios created yet'"
                        description="Create a portfolio for a client to start tracking their investments."
                        :action="auth()->user()->can('create portfolios') ? new Illuminate\Support\HtmlString('<a href='.route('portfolios.create').' class=btn-primary wire:navigate>Create Portfolio</a>') : null" />
                </div>
            @else
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left">
                                <button type="button" wire:click="sortBy('client_id')" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700">Client {!! $sortIcon('client_id') !!}</button>
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Fixed Debt</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Equity</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Value</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Instruments</th>
                            <th scope="col" class="px-5 py-3 text-left">
                                <button type="button" wire:click="sortBy('created_at')" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700">Created {!! $sortIcon('created_at') !!}</button>
                            </th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($portfolios as $portfolio)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition" wire:key="pf-{{ $portfolio->id }}">
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <a href="{{ route('portfolios.show', $portfolio) }}" wire:navigate class="block">
                                        <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $portfolio->client?->full_name }}</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $portfolio->client?->client_id }}</span>
                                    </a>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_money($portfolio->total_fixed_debt) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_money($portfolio->total_equity_value) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-slate-100">{{ format_money($portfolio->total_portfolio_value) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $portfolio->fixedDebtInstruments()->count() }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_date($portfolio->created_at) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('portfolios.show', $portfolio) }}" wire:navigate class="btn-ghost btn-sm">View</a>
                                        @can('edit portfolios')
                                            <a href="{{ route('portfolios.edit', $portfolio) }}" wire:navigate class="btn-ghost btn-sm">Edit</a>
                                        @endcan
                                        @can('delete portfolios')
                                            <x-confirm-dialog
                                                name="delete-portfolio-{{ $portfolio->id }}"
                                                title="Delete portfolio"
                                                message="Delete this portfolio? Its instruments, history and equities will be hidden."
                                                buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                wireClick="delete({{ $portfolio->id }})"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($portfolios->hasPages())
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
                {{ $portfolios->links() }}
            </div>
        @endif
    </div>
</div>