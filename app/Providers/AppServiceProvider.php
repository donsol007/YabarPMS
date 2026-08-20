<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (Login $event) {
            activity()
                ->causedBy($event->user)
                ->withProperties(['email' => $event->user?->email, 'guard' => $event->guard])
                ->log('logged in');
        });

        Event::listen(function (Logout $event) {
            activity()
                ->causedBy($event->user)
                ->withProperties(['email' => $event->user?->email, 'guard' => $event->guard])
                ->log('logged out');
        });

        Event::listen(function (Failed $event) {
            activity()
                ->byAnonymous()
                ->withProperties(['email' => $event->credentials['email'] ?? null, 'guard' => $event->guard])
                ->log('failed login attempt');
        });
    }
}