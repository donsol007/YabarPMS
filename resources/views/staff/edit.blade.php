<x-app-layout>
    <x-slot name="title">Edit Staff</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Staff Account</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update {{ $user->name }}'s details. Leave the password blank to keep the current one.</p>
    </div>

    <div class="card max-w-xl">
        <div class="card-header">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Staff Details</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('staff.update', $user) }}" x-data="{ password: '', confirm: '' }" @submit="if (password !== confirm) { $event.preventDefault(); $refs.matchError.classList.remove('hidden') }">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" class="input mt-1 w-full">
                            <option value="staff" @selected(old('role', $user->roles->first()?->name) === 'staff')>Staff</option>
                            <option value="admin" @selected(old('role', $user->roles->first()?->name) === 'admin')>Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="password" :value="__('New Password (optional)')" />
                            <x-text-input id="password" type="password" name="password" x-model="password" class="mt-1 w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Min 8 chars, mixed case &amp; numbers. Leave blank to keep current.</p>
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation" x-model="confirm" class="mt-1 w-full" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <p x-ref="matchError" class="hidden text-sm text-red-600 dark:text-red-400">Passwords do not match.</p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('staff.index') }}" class="btn-secondary" wire:navigate>Cancel</a>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>