@php
    $clients = $this->clients;
    $sortIcon = fn ($field) => $this->sortField === $field
        ? ($this->sortDirection === 'asc' ? '↑' : '↓')
        : '';
@endphp

<div>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">All clients</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $clients->total() }} registered clients</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search clients…"
                           class="w-56 rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 pl-9 pr-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500" />
                </div>
                @can('create clients')
                    <button type="button" @click="$dispatch('open-modal', 'share-registration-link')" class="btn-secondary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 015.656 0l2.172 2.172a4 4 0 010 5.657l-1.086 1.086a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-2.172-2.172a4 4 0 010-5.657l1.086-1.086a4 4 0 015.656 0M8 8l8 8" />
                        </svg>
                        Share Link
                    </button>
                    <a href="{{ route('clients.create') }}" class="btn-primary" wire:navigate>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Client
                    </a>
                @endcan
            </div>
        </div>

        <div class="overflow-x-auto">
            @if ($clients->isEmpty())
                <div class="p-6">
                    <x-empty-state
                        :title="$search ? 'No clients match your search' : 'No clients registered yet'"
                        description="Register your first client to get started."
                        :action="auth()->user()->can('create clients') ? new Illuminate\Support\HtmlString('<a href='.route('clients.create').' class=btn-primary wire:navigate>Register Client</a>') : null" />
                </div>
            @else
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left">
                                <button type="button" wire:click="sortBy('client_id')" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700">Client ID {!! $sortIcon('client_id') !!}</button>
                            </th>
                            <th scope="col" class="px-5 py-3 text-left">
                                <button type="button" wire:click="sortBy('surname')" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700">Name {!! $sortIcon('surname') !!}</button>
                            </th>
                            <th scope="col" class="px-5 py-3 text-left">
                                <button type="button" wire:click="sortBy('email')" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 hover:text-slate-700">Email {!! $sortIcon('email') !!}</button>
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Phone</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Investment</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Portfolio</th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($clients as $client)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition" wire:key="client-{{ $client->id }}">
                                <td class="px-5 py-3 whitespace-nowrap text-sm font-semibold text-brand-600 dark:text-brand-400">{{ $client->client_id }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm font-medium text-slate-800 dark:text-slate-100">{{ $client->full_name }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $client->email }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $client->mobile_number }}</td>
                                <td class="px-5 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ format_money($client->amount_to_invest) }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if ($client->portfolio)
                                        <x-badge color="green">Has portfolio</x-badge>
                                    @else
                                        <x-badge color="gray">No portfolio</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if ($client->isPending())
                                        <x-badge color="amber">Pending</x-badge>
                                    @else
                                        <x-badge color="green">Approved</x-badge>
                                    @endif
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($client->isPending() && auth()->user()->can('edit clients'))
                                            <button type="button"
                                                    wire:click="approve({{ $client->id }})"
                                                    class="btn-ghost btn-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">Approve</button>
                                        @endif
                                        <a href="{{ route('clients.edit', $client) }}" wire:navigate class="btn-ghost btn-sm">Edit</a>
                                        @can('delete clients')
                                            <x-confirm-dialog
                                                name="delete-client-{{ $client->id }}"
                                                title="Delete client"
                                                message="Delete client {{ $client->client_id }} ({{ $client->full_name }})? This action can be reversed."
                                                buttonClass="btn-ghost btn-sm text-red-600 hover:text-red-700 dark:text-red-400"
                                                wireClick="delete({{ $client->id }})"
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

        @if ($clients->hasPages())
            <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    @can('create clients')
        <x-modal name="share-registration-link" :show="false" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">Share Registration Link</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Share this link with a prospective client. They can fill in their details themselves and the record will appear in your client list.</p>
                <div class="mt-4 flex items-center gap-2" x-data="{ copied: false, copyLink() { const el = $refs.link; el.select(); el.setSelectionRange(0, 99999); navigator.clipboard?.writeText(el.value).catch(() => document.execCommand('copy')); copied = true; setTimeout(() => copied = false, 2000); } }">
                    <input type="text" readonly x-ref="link" :value="@js(route('client.register'))" class="input w-full" @click="copyLink()" />
                    <button type="button" class="btn-primary shrink-0" @click="copyLink()">
                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                    </button>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="btn-secondary" @click="$dispatch('close-modal', 'share-registration-link')">Close</button>
                </div>
            </div>
        </x-modal>
    @endcan
</div>