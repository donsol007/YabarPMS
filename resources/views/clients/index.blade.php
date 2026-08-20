<x-app-layout>
    <x-slot name="title">Client Information</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Client Information</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage all registered clients and their personal, banking and employment details.</p>
    </div>

    <livewire:client-index />
</x-app-layout>