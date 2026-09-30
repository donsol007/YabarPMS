<x-app-layout>
    <x-slot name="title">Client Information</x-slot>

    @php
        $show = fn ($value) => filled($value) ? $value : '—';
        $state = $client->stateOfOrigin?->name;
        $lga = $client->lga?->name ?? $client->lga_name;
        $bank = $client->bank?->name ?? $client->bank_name;
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $client->full_name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $client->client_id }} · Registered {{ format_datetime($client->created_at) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('clients.index') }}" class="btn-secondary" wire:navigate>Back</a>
            @can('edit clients')
                <a href="{{ route('clients.edit', $client) }}" class="btn-primary" wire:navigate>Edit</a>
            @endcan
        </div>
    </div>

    @can('edit clients')
        <x-card title="Portfolio Access" description="Secure link and access code the client uses to view their portfolio." class="mb-6">
            @if ($client->hasPortfolioAccess())
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Access Code</span>
                    <code class="rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $client->portfolio_access_code }}</code>
                </div>
                @include('clients._portfolio-access', ['client' => $client])
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    No access code has been set for this client.
                    @if (Route::has('clients.edit'))
                        <a href="{{ route('clients.edit', $client) }}" class="font-semibold text-brand-600 dark:text-brand-400" wire:navigate>Add access code</a>
                    @endif
                </p>
            @endif
        </x-card>
    @endcan

    {{-- Personal Information --}}
    <x-card title="Personal Information" description="Identity and personal details of the client." class="mb-6">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <x-badge color="brand">{{ $client->client_id }}</x-badge>
            @if ($client->isPending())
                <x-badge color="amber">Pending</x-badge>
            @else
                <x-badge color="green">Approved</x-badge>
            @endif
            @if ($client->portfolio)
                <x-badge color="green">Has portfolio</x-badge>
            @else
                <x-badge color="gray">No portfolio</x-badge>
            @endif
        </div>
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Surname</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->surname) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">First Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->first_name) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Middle Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->middle_name) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Sex</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->sex) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Date of Birth</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ format_date($client->date_of_birth) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Mobile Number</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->mobile_number) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Email Address</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->email) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Mother's Maiden Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->mother_maiden_name) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Marital Status</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->marital_status) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Religion</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->religion) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">State of Origin</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($state) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Local Government Area</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($lga) }}</dd>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Residential Address</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->residential_address) }}</dd>
            </div>
        </dl>
    </x-card>

    {{-- Investment Information --}}
    <x-card title="Investment Information" class="mb-6">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Amount to Invest</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ format_money($client->amount_to_invest) }}</dd>
            </div>
        </dl>
    </x-card>

    {{-- Banking Information --}}
    <x-card title="Banking Information" class="mb-6">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Bank Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($bank) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Account Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->account_name) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Account Number</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->account_number) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Account Type</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->account_type) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">BVN</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->bvn) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Date of Account Opening</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ format_date($client->account_opening_date) }}</dd>
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Bank Address</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->bank_address) }}</dd>
            </div>
        </dl>
    </x-card>

    {{-- Employment Information --}}
    <x-card title="Employment Information" class="mb-6">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Occupation</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->occupation) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Employer's Name</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->employer_name) }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Employer's Address</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->employer_address) }}</dd>
            </div>
        </dl>
    </x-card>

    {{-- Additional Information --}}
    <x-card title="Additional Information" class="mb-6">
        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Hobbies</dt>
                <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->hobbies) }}</dd>
            </div>
        </dl>
    </x-card>

    {{-- Next of Kin --}}
    <x-card title="Next of Kin" class="mb-6">
        @if ($client->nextOfKin)
            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Name</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->nextOfKin->name) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Phone Number</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->nextOfKin->phone) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Email Address</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->nextOfKin->email) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Relationship</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->nextOfKin->relationship) }}</dd>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Address</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-100">{{ $show($client->nextOfKin->address) }}</dd>
                </div>
            </dl>
        @else
            <p class="text-sm text-slate-500 dark:text-slate-400">No next of kin recorded.</p>
        @endif
    </x-card>

    {{-- Documents --}}
    <x-card title="Documents" description="ID Card, Utility Bill and Signature." class="mb-6">
        <div class="grid gap-4 lg:grid-cols-3">
            @foreach (\App\Models\Document::TYPES as $type => $label)
                @php($doc = $client->documents->firstWhere('type', $type))
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $label }}</p>
                    @if ($doc)
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $doc->original_name }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($doc->size / 1024, 1) }} KB</p>
                            </div>
                        </div>
                        <a href="{{ route('clients.documents.download', [$client, $doc]) }}" class="btn-secondary btn-sm mt-3 w-full justify-center">Download</a>
                    @else
                        <p class="mt-2 text-sm text-slate-400">Not uploaded.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </x-card>
</x-app-layout>