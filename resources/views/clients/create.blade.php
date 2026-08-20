<x-app-layout>
    <x-slot name="title">Register Client</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Register New Client</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Client ID will be auto-generated as <span class="font-medium">YFC-{{ str_pad(App\Models\Client::query()->count() + 1, 4, '0', STR_PAD_LEFT) }}</span>.</p>
    </div>

    @include('clients._form', ['client' => new App\Models\Client, 'action' => route('clients.store'), 'method' => 'POST'])
</x-app-layout>