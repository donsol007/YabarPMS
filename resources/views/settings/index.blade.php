<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Application-wide configuration. Admin only.</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">General Settings</h3>
            </div>
            <div class="card-body">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="company_name" :value="__('Company Name')" />
                        <x-text-input id="company_name" name="company_name" value="{{ old('company_name', setting('company_name', 'Yabar Finance Consult Limited')) }}" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="currency_symbol" :value="__('Currency Symbol')" />
                        <x-text-input id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', setting('currency_symbol', '₦')) }}" class="mt-1 w-full" required maxlength="10" />
                        <x-input-error :messages="$errors->get('currency_symbol')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="due_notice_days" :value="__('Due Notice Days')" />
                        <x-text-input id="due_notice_days" type="number" min="1" max="90" name="due_notice_days" value="{{ old('due_notice_days', setting('due_notice_days', 7)) }}" class="mt-1 w-full" required />
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Days ahead that due-payment notifications are sent.</p>
                        <x-input-error :messages="$errors->get('due_notice_days')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="upload_max_size" :value="__('Upload Max Size (MB)')" />
                        <x-text-input id="upload_max_size" type="number" min="1" max="50" name="upload_max_size" value="{{ old('upload_max_size', setting('upload_max_size', 5)) }}" class="mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('upload_max_size')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Branding</h3>
            </div>
            <div class="card-body">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div x-data="{ preview: @js(setting('company_logo') ? asset('storage/'.setting('company_logo')) : '') }">
                        <x-input-label for="company_logo" :value="__('Company Logo')" />
                        <div class="mt-1 flex h-24 items-center justify-center rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 overflow-hidden">
                            <img x-show="preview" :src="preview" alt="Company logo" x-cloak class="max-h-full max-w-full object-contain p-2" />
                            <span x-show="!preview" class="text-xs text-slate-400">No logo uploaded</span>
                        </div>
                        <input id="company_logo" type="file" name="company_logo" accept=".png,.jpg,.jpeg,.svg,.webp"
                               class="mt-2 block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 dark:file:bg-brand-900/40 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-700 dark:file:text-brand-300 hover:file:bg-brand-100 dark:hover:file:bg-brand-900/60"
                               x-on:change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PNG, JPG, SVG or WebP. Recommended 512×512. Max 2MB.</p>
                        <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
                    </div>

                    <div x-data="{ preview: @js(setting('company_favicon') ? asset('storage/'.setting('company_favicon')) : '') }">
                        <x-input-label for="company_favicon" :value="__('Favicon')" />
                        <div class="mt-1 flex h-24 items-center justify-center rounded-lg border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 overflow-hidden">
                            <img x-show="preview" :src="preview" alt="Favicon" x-cloak class="max-h-full max-w-full object-contain p-2" />
                            <span x-show="!preview" class="text-xs text-slate-400">No favicon uploaded</span>
                        </div>
                        <input id="company_favicon" type="file" name="company_favicon" accept=".png,.ico,.svg"
                               class="mt-2 block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 dark:file:bg-brand-900/40 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-700 dark:file:text-brand-300 hover:file:bg-brand-100 dark:hover:file:bg-brand-900/60"
                               x-on:change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PNG, ICO or SVG. Recommended 32×32. Max 512KB.</p>
                        <x-input-error :messages="$errors->get('company_favicon')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>
</x-app-layout>