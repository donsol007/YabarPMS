<x-app-layout>
    <x-slot name="title">Create Portfolio</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Portfolio</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A portfolio is created for a client that does not yet have one.</p>
    </div>

    <div class="card max-w-xl">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Portfolio Details</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('portfolios.store') }}">
                @csrf
                <x-input-label for="client_id" :value="__('Client Name')" />
                <select id="client_id" name="client_id" class="input mt-1 w-full" required>
                    <option value="">Select a client without a portfolio</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }} ({{ $client->client_id }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />

                @if ($clients->isEmpty())
                    @php($pendingClients = \App\Models\Client::pending()->doesntHave('portfolio')->count())
                    <p class="mt-3 rounded-lg bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                        @if ($pendingClients > 0)
                            {{ $pendingClients }} client(s) are awaiting approval. Approve their registration before creating a portfolio.
                        @else
                            All clients already have a portfolio. Register a new client first.
                        @endif
                    </p>
                @endif

                <div class="mt-5 flex items-center justify-end gap-3">
                    <a href="{{ route('portfolios.index') }}" class="btn-secondary" wire:navigate>Cancel</a>
                    <button type="submit" class="btn-primary" {{ $clients->isEmpty() ? 'disabled' : '' }}>Create Portfolio</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>