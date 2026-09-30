<x-public-layout>
    <x-slot name="title">Portfolio Access</x-slot>

    <div class="mx-auto max-w-md">
        <div class="card overflow-hidden">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-900/40 text-brand-600 dark:text-brand-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Secure Portfolio Access</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Enter your access code to continue</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('portfolio.access.unlock', $token) }}" class="card-body space-y-4">
                @csrf

                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Please enter the access code provided by
                    {{ setting('company_name', 'your investment advisor') }} to view the portfolio
                    details for <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $client->full_name }}</span>.
                </p>

                <div>
                    <x-input-label for="access_code" :value="__('Access Code')" />
                    <x-text-input id="access_code"
                                  name="access_code"
                                  type="password"
                                  inputmode="text"
                                  autocomplete="off"
                                  autofocus
                                  required
                                  class="mt-1 w-full"
                                  placeholder="Enter access code" />
                    <x-input-error :messages="$errors->get('access_code')" class="mt-2" />
                </div>

                <button type="submit" class="btn-primary w-full">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    View My Portfolio
                </button>

                <p class="text-center text-xs text-slate-400 dark:text-slate-500">
                    If you do not have an access code, please contact {{ setting('company_name', 'us') }}.
                </p>
            </form>
        </div>
    </div>
</x-public-layout>