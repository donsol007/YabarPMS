<x-app-layout>
    <x-slot name="title">Profile</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Profile</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your personal information and password.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 max-w-5xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Profile Information</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Update your account's profile information.</p>
                </div>
            </div>
            <div class="card-body">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Change Password</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Ensure your account is using a strong password.</p>
                </div>
            </div>
            <div class="card-body">
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>
</x-app-layout>