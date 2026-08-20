<x-app-layout>
    <x-slot name="title">Edit Client</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit {{ $client->full_name }}</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $client->client_id }} · Registered {{ format_datetime($client->created_at) }}</p>
    </div>

    @if ($client->isPending())
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-900/20 p-4 text-sm text-amber-800 dark:text-amber-200">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>
                This client registered through the public link and is awaiting approval. Portfolio details cannot be added until the registration is approved.
                @can('edit clients')
                    <form method="POST" action="{{ route('clients.approve', $client) }}" class="mt-2 inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Approve Registration
                        </button>
                    </form>
                @endcan
            </p>
        </div>
    @endif

    @include('clients._form', ['client' => $client, 'action' => route('clients.update', $client), 'method' => 'PUT'])
</x-app-layout>