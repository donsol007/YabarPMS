<x-app-layout>
    <x-slot name="title">Staff Accounts</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Staff Accounts</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Admin-only. Create and manage staff accounts.</p>
        </div>
        <a href="{{ route('staff.create') }}" class="btn-primary" wire:navigate>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Staff
        </a>
    </div>

    <div class="card">
        @if ($staff->isEmpty())
            <div class="p-6">
                <x-empty-state title="No staff accounts yet" description="Create a staff account to get started." />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Email</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Phone</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Role</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Joined</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($staff as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="px-5 py-3 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $user->name }}</td>
                                <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300">{{ $user->phone ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <x-badge :color="$user->isAdmin() ? 'brand' : 'gray'">{{ ucfirst($user->roles->first()?->name ?? 'user') }}</x-badge>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600 dark:text-slate-300">{{ format_date($user->created_at) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('staff.edit', $user) }}" class="btn-secondary btn-sm" wire:navigate>Edit</a>
                                        @if ($user->id !== auth()->id())
                                            <x-confirm-dialog
                                                name="delete-staff-{{ $user->id }}"
                                                title="Delete staff account"
                                                message="Delete the account for {{ $user->name }}? This cannot be undone."
                                                buttonText="Delete"
                                                buttonClass="btn-danger btn-sm"
                                                action="{{ route('staff.destroy', $user) }}"
                                            />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($staff->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 px-5 py-3">{{ $staff->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>