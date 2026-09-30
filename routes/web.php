<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPortfolioAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('dashboard', DashboardController::class)->name('dashboard.index');

    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/approve', [ClientController::class, 'approve'])
        ->name('clients.approve');

    Route::post('clients/{client}/portfolio-access/regenerate', [ClientController::class, 'regeneratePortfolioAccess'])
        ->name('clients.portfolio-access.regenerate');
    Route::post('clients/{client}/portfolio-access/email', [ClientController::class, 'emailPortfolioAccess'])
        ->name('clients.portfolio-access.email');

    Route::middleware('role:admin')->group(function () {
        Route::delete('clients/{client}/documents/{document}', [ClientController::class, 'destroyDocument'])
            ->name('clients.documents.destroy');

        Route::resource('portfolios', PortfolioController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('index');
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::post('/', [StaffController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::put('/{user}', [StaffController::class, 'update'])->name('update');
            Route::delete('/{user}', [StaffController::class, 'destroy'])->name('destroy');
        });

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('settings/email', [SettingsController::class, 'emailIndex'])->name('settings.email.index');
        Route::put('settings/email', [SettingsController::class, 'emailUpdate'])->name('settings.email.update');
    });

    Route::get('clients/{client}/documents/{document}/download', [ClientController::class, 'downloadDocument'])
        ->name('clients.documents.download');

    Route::get('portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
    Route::get('portfolios/{portfolio}', [PortfolioController::class, 'show'])->name('portfolios.show');
    Route::get('portfolios/{portfolio}/print/{instrument}', [PortfolioController::class, 'printInstrument'])->name('portfolios.instruments.print');
    Route::get('portfolios/{portfolio}/pdf/{instrument}', [PortfolioController::class, 'exportInstrumentPdf'])->name('portfolios.instruments.pdf');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/portfolio', [ReportController::class, 'portfolio'])->name('reports.portfolio');
    Route::post('reports/portfolio/email', [ReportController::class, 'sendPortfolio'])->name('reports.portfolio.email');
    Route::post('reports/client', [ReportController::class, 'client'])->name('reports.client');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::prefix('portfolio-access')->name('portfolio.access.')->group(function () {
    Route::get('{token}', [ClientPortfolioAccessController::class, 'show'])
        ->name('show');
    Route::post('{token}', [ClientPortfolioAccessController::class, 'unlock'])
        ->middleware('throttle:10,1')
        ->name('unlock');
    Route::get('{token}/view', [ClientPortfolioAccessController::class, 'view'])
        ->name('view');
    Route::get('{token}/pdf', [ClientPortfolioAccessController::class, 'pdf'])
        ->name('pdf');
    Route::post('{token}/lock', [ClientPortfolioAccessController::class, 'lock'])
        ->name('lock');
});

require __DIR__.'/auth.php';
