<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404 — Page Not Found</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">
            <p class="text-7xl font-bold text-brand-600 dark:text-brand-400">404</p>
            <h1 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white">Page not found</h1>
            <p class="mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">The page you are looking for doesn't exist or may have been moved.</p>
            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn-primary">Back to Dashboard</a>
                <button type="button" class="btn-secondary" onclick="history.back()">Go Back</button>
            </div>
        </div>
    </body>
</html>