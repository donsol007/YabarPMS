<x-app-layout>
    <x-slot name="title">Email Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Email Settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure the SMTP account used to send reports to clients. Admin only.</p>
    </div>

    <form method="POST" action="{{ route('settings.email.update') }}" class="max-w-2xl space-y-6"
          x-data="{ saving: false }" @submit="saving = true">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">SMTP Server</h3>
            </div>
            <div class="card-body">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="mail_host" :value="__('SMTP Host')" />
                        <x-text-input id="mail_host" name="mail_host" value="{{ old('mail_host', setting('mail_host')) }}" class="mt-1 w-full" placeholder="smtp.gmail.com" required />
                        <x-input-error :messages="$errors->get('mail_host')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mail_port" :value="__('Port')" />
                        <x-text-input id="mail_port" type="number" min="1" max="65535" name="mail_port" value="{{ old('mail_port', setting('mail_port', 587)) }}" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('mail_port')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mail_encryption" :value="__('Encryption')" />
                        <select id="mail_encryption" name="mail_encryption" class="input mt-1 w-full" required>
                            @foreach (['tls' => 'TLS (STARTTLS)', 'ssl' => 'SSL', 'none' => 'None'] as $value => $label)
                                <option value="{{ $value }}" {{ old('mail_encryption', setting('mail_encryption', 'tls')) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('mail_encryption')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mail_username" :value="__('Username')" />
                        <x-text-input id="mail_username" name="mail_username" value="{{ old('mail_username', setting('mail_username')) }}" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('mail_username')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mail_password" :value="__('Password')" />
                        <x-text-input id="mail_password" type="password" name="mail_password" class="mt-1 w-full" placeholder="Leave blank to keep current password" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('mail_password')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Sender Details</h3>
            </div>
            <div class="card-body">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="mail_from_address" :value="__('From Email Address')" />
                        <x-text-input id="mail_from_address" type="email" name="mail_from_address" value="{{ old('mail_from_address', setting('mail_from_address')) }}" class="mt-1 w-full" placeholder="reports@example.com" required />
                        <x-input-error :messages="$errors->get('mail_from_address')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="mail_from_name" :value="__('From Name')" />
                        <x-text-input id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', setting('mail_from_name', setting('company_name'))) }}" class="mt-1 w-full" />
                        <x-input-error :messages="$errors->get('mail_from_name')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary" :disabled="saving">
                <svg x-show="saving" x-cloak class="h-3.5 w-3.5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="saving ? 'Saving…' : 'Save Email Settings'"></span>
            </button>
        </div>
    </form>
</x-app-layout>