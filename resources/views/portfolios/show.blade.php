<x-app-layout>
    <x-slot name="title">{{ $portfolio->client->full_name }} — Portfolio</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $portfolio->client->full_name }}</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $portfolio->client->client_id }} · Created {{ format_date($portfolio->created_at) }}</p>
    </div>

    <livewire:portfolio-manager :portfolio="$portfolio" />
</x-app-layout>