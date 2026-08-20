<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ setting('company_name', config('app.name', 'Yabar ERP')) }}</title>

        <link rel="icon" type="image/x-icon" href="{{ setting('company_favicon') ? asset('storage/'.setting('company_favicon')) : asset('favicon.ico') }}">

        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }">
            {{-- Sidebar --}}
            <aside class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 bg-slate-900 text-slate-300 flex flex-col lg:static lg:translate-x-0 transition-transform duration-200"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

                <div class="flex items-center justify-between px-5 h-16 border-b border-slate-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5" wire:navigate>
                        @if (setting('company_logo'))
                            <img src="{{ asset('storage/'.setting('company_logo')) }}" alt="{{ setting('company_name') }}" class="h-9 w-9 rounded-lg object-contain">
                        @else
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white font-bold">Y</div>
                        @endif
                        <div class="leading-tight">
                            <span class="block text-sm font-bold text-white">{{ setting('company_name', 'Yabar ERP') }}</span>
                            <span class="block text-[11px] text-slate-400">Portfolio Management</span>
                        </div>
                    </a>
                    <button type="button" class="lg:hidden text-slate-400 hover:text-white" @click="sidebarOpen = false" aria-label="Close sidebar">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    <x-sidebar-link :route="'dashboard'" :active="request()->routeIs('dashboard*')" label="Dashboard" icon="home" />
                    <x-sidebar-link :route="'portfolios.index'" :active="request()->routeIs('portfolios*')" label="Client Portfolio" icon="briefcase" />
                    <x-sidebar-link :route="'clients.index'" :active="request()->routeIs('clients*')" label="Client Information" icon="users" />
                    <x-sidebar-link :route="'reports.index'" :active="request()->routeIs('reports*')" label="Reports" icon="document" />
                    <x-sidebar-link :route="'profile'" :active="request()->routeIs('profile')" label="Profile" icon="user" />

                    @can('manage settings')
                        <x-sidebar-link :route="'settings.index'" :active="request()->routeIs('settings.index')" label="Settings" icon="cog" />
                        <x-sidebar-link :route="'settings.email.index'" :active="request()->routeIs('settings.email*')" label="Email Settings" icon="mail" />
                    @endcan

                    @can('manage staff')
                        <x-sidebar-link :route="'staff.index'" :active="request()->routeIs('staff*')" label="Staff Accounts" icon="shield" />
                    @endcan

                    <div class="pt-4 mt-4 border-t border-slate-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>

                <div class="px-5 py-4 border-t border-slate-800 text-xs text-slate-500">
                    © {{ now()->year }} {{ setting('company_name', 'Yabar ERP') }}
                </div>
            </aside>

            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-transition:opacity class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false"></div>

            {{-- Main column --}}
            <div class="flex min-w-0 flex-1 flex-col">
                {{-- Top navigation --}}
                <header class="sticky top-0 z-20 h-16 border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur flex items-center gap-3 px-4 sm:px-6">
                    <button type="button" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 lg:hidden" @click="sidebarOpen = true" aria-label="Open sidebar">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Breadcrumbs --}}
                    <x-breadcrumbs class="hidden sm:flex" />

                    <div class="ml-auto flex items-center gap-2 sm:gap-3">
                        <livewire:global-search />

                        <livewire:notifications-bell />

                        <button type="button"
                                class="rounded-lg p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                x-data=""
                                x-on:click="$store.theme.toggle()"
                                aria-label="Toggle dark mode">
                            <svg x-show="!$store.theme.dark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="$store.theme.dark" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        {{-- Profile dropdown --}}
                        <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ auth()->user()->initials() }}</span>
                                <span class="hidden md:block text-sm font-medium text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition @click.outside="open = false"
                                 class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-lg py-1 z-50">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                    <span class="mt-1 inline-flex items-center rounded-full bg-brand-50 dark:bg-brand-900/50 px-2 py-0.5 text-[11px] font-semibold capitalize text-brand-700 dark:text-brand-300">
                                        {{ auth()->user()->roles->first()?->name ?? 'user' }}
                                    </span>
                                </div>
                                <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                    Profile
                                </a>
                                @can('manage settings')
                                    <a href="{{ route('settings.index') }}" wire:navigate class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                        Settings
                                    </a>
                                @endcan
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toasts --}}
        <div x-data="toastHandler" x-cloak @remove-toast.window="remove($event.detail)">
            <div class="fixed top-4 right-4 z-[100] space-y-2 w-full max-w-sm">
                <template x-for="toast in toasts" :key="toast.id">
                    <div x-show="true" x-transition
                         class="flex items-start gap-3 rounded-xl border p-4 shadow-lg"
                         :class="{
                            'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100': toast.type === 'success',
                            'bg-white dark:bg-slate-800 border-red-200 dark:border-red-900 text-red-700 dark:text-red-300': toast.type === 'error',
                            'bg-white dark:bg-slate-800 border-amber-200 dark:border-amber-900 text-amber-700 dark:text-amber-300': toast.type === 'warning',
                         }">
                        <span class="mt-0.5">
                            <svg x-show="toast.type === 'success'" class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg x-show="toast.type !== 'success'" class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <p class="flex-1 text-sm font-medium" x-text="toast.message"></p>
                        <button type="button" class="text-slate-400 hover:text-slate-600" @click="$dispatch('remove-toast', toast.id)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div data-toast-flash="{{ session('toast') ? json_encode(session('toast')) : '' }}"></div>

        @livewireScripts
    </body>
</html>