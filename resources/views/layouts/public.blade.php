<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — ' : '' }}{{ setting('company_name', config('app.name', 'Yabar ERP')) }}</title>

        <link rel="icon" type="image/x-icon" href="{{ setting('company_favicon') ? asset('storage/'.setting('company_favicon')) : asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 dark:bg-slate-950">
        <header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
            <div class="mx-auto flex max-w-5xl items-center gap-3 px-4 py-4 sm:px-6">
                @if (setting('company_logo'))
                    <img src="{{ asset('storage/'.setting('company_logo')) }}" alt="{{ setting('company_name') }}" class="h-10 w-10 rounded-xl object-contain">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white">Y</div>
                @endif
                <div class="leading-tight">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ setting('company_name', 'Yabar ERP') }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Client Portfolio Portal</p>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
            {{ $slot }}
        </main>

        <footer class="mx-auto max-w-5xl px-4 pb-10 sm:px-6">
            <p class="text-center text-xs text-slate-400 dark:text-slate-500">
                © {{ now()->year }} {{ setting('company_name', 'Yabar ERP') }}. All rights reserved.
            </p>
        </footer>
    </body>
</html>