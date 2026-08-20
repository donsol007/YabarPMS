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
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen justify-center bg-slate-100 dark:bg-slate-950 px-4 py-10 sm:px-6">
            <div class="w-full max-w-4xl">
                <div class="mb-6 flex flex-col items-center">
                    @if (setting('company_logo'))
                        <img src="{{ asset('storage/'.setting('company_logo')) }}" alt="{{ setting('company_name') }}" class="h-14 w-14 rounded-2xl object-contain">
                    @else
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-2xl font-bold text-white shadow-lg shadow-brand-600/30">Y</div>
                    @endif
                    <h1 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{{ setting('company_name', 'Yabar ERP') }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Client Registration</p>
                </div>

                <div class="rounded-2xl bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xl border border-slate-200 dark:border-slate-800">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                    © {{ now()->year }} {{ setting('company_name', 'Yabar ERP') }}
                </p>
            </div>
        </div>

        @livewireScripts
    </body>
</html>