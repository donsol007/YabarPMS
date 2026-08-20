<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Breadcrumb --}}
    <nav class="mb-3 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        <span class="font-medium text-slate-700 dark:text-slate-300">Dashboard</span>
    </nav>

    {{-- Page header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Monitor your investment portfolio performance at a glance.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn-secondary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Generate Report
            </a>
            <a href="{{ route('clients.create') }}" class="btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                New Client
            </a>
        </div>
    </div>

    {{-- Welcome banner --}}
    <div class="card mb-6 overflow-hidden border-0 bg-gradient-to-r from-brand-700 via-brand-600 to-violet-600">
        <div class="card-body p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-brand-200">{{ now()->format('l, j F Y') }}</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-white">Welcome back, {{ auth()->user()->name }}</h2>

                </div>

            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card
            label="Total Clients"
            :value="number_format($stats['total_clients'])"
            icon="users"
            accent="brand"
            hint="All registered clients"
            :trend="['label' => '+'.number_format($stats['recent_registrations']).' this week', 'color' => 'text-emerald-600 dark:text-emerald-400', 'direction' => 'up']"
        />
        <x-stat-card
            label="Total Investment"
            :value="format_money($stats['total_investment'])"
            icon="cash"
            accent="green"
            hint="Fixed debt + equity value"
            :trend="['label' => 'Across '.number_format($stats['total_active_portfolios']).' portfolios', 'color' => 'text-emerald-600 dark:text-emerald-400', 'direction' => 'up']"
        />
        <x-stat-card
            label="Active Portfolios"
            :value="number_format($stats['total_active_portfolios'])"
            icon="briefcase"
            accent="violet"
            hint="With at least one active instrument"
        />
        <x-stat-card
            label="Recent Registrations"
            :value="number_format($stats['recent_registrations'])"
            icon="clock"
            accent="amber"
            hint="New clients in the last 7 days"
        />
    </div>

    {{-- Charts row --}}
    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Investment Distribution</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Total CP (capital protected) vs equity value across all clients.</p>
                </div>
            </div>
            <div class="card-body">
                <div
                    x-data="chart"
                    x-init="load('pie', @js($distribution))"
                    class="h-80"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Portfolio Performance</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Total investment value over the last 12 months.</p>
                </div>
            </div>
            <div class="card-body">
                <div
                    x-data="chart"
                    x-init="load('line', @js($performance))"
                    class="h-80"></div>
            </div>
        </div>
    </div>

    {{-- Monthly investments --}}
    <div class="mt-6">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Monthly Investments</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Client investments registered per month.</p>
                </div>
            </div>
            <div class="card-body">
                <div
                    x-data="chart"
                    x-init="load('bar', @js($monthly))"
                    class="h-80"></div>
            </div>
        </div>
    </div>

    </x-app-layout>
