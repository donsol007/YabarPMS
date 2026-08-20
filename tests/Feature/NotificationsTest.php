<?php

use App\Models\FixedDebtInstrument;
use App\Models\PaymentBreakdown;
use App\Models\PaymentHistory;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\ClientRegisteredNotification;
use App\Notifications\PaymentDueNotification;
use Illuminate\Support\Facades\Notification;

test('users are notified when a client is registered', function () {
    Notification::fake();

    $admin = adminUser();
    staffUser();

    $this->actingAs($admin)
        ->post('/clients', validClientPayload());

    $client = \App\Models\Client::where('email', 'john.doe@example.com')->first();

    Notification::assertSentTo(User::all(), ClientRegisteredNotification::class, function ($notification) use ($client) {
        return $notification->client->id === $client->id;
    });
});

test('due payment command notifies about upcoming unpaid breakdowns', function () {
    Notification::fake();

    $admin = adminUser();

    $portfolio = Portfolio::factory()->create();
    $instrument = FixedDebtInstrument::create([
        'portfolio_id' => $portfolio->id,
        'description' => 'CP 2026',
        'amount' => 1000000,
        'status' => 'Active',
    ]);

    PaymentBreakdown::create([
        'fixed_debt_instrument_id' => $instrument->id,
        'roi' => 12.5,
        'amount' => 125000,
        'payment_date' => now()->addDays(3)->toDateString(),
        'status' => 'unpaid',
    ]);

    $this->artisan('notifications:due-payments')
        ->expectsOutputToContain('1 due-payment notification groups')
        ->assertExitCode(0);

    Notification::assertSentTo($admin, PaymentDueNotification::class, function ($notification) {
        return str_contains($notification->ref, 'breakdown-');
    });
});

test('due payment command does not duplicate notifications', function () {
    adminUser();

    $portfolio = Portfolio::factory()->create();
    $instrument = FixedDebtInstrument::create([
        'portfolio_id' => $portfolio->id,
        'description' => 'CP 2026',
        'amount' => 1000000,
        'status' => 'Active',
    ]);

    PaymentBreakdown::create([
        'fixed_debt_instrument_id' => $instrument->id,
        'roi' => 12.5,
        'amount' => 125000,
        'payment_date' => now()->addDays(3)->toDateString(),
        'status' => 'unpaid',
    ]);

    $this->artisan('notifications:due-payments')->assertExitCode(0);
    $this->artisan('notifications:due-payments')->assertExitCode(0);

    expect(\Illuminate\Notifications\DatabaseNotification::where('type', PaymentDueNotification::class)->count())->toBe(1);
});

test('due payment command notifies about unpaid payment histories', function () {
    Notification::fake();

    $admin = adminUser();

    $portfolio = Portfolio::factory()->create();

    PaymentHistory::create([
        'portfolio_id' => $portfolio->id,
        'date' => now()->addDays(2)->toDateString(),
        'amount_paid' => 50000,
        'payment_status' => 'unpaid',
    ]);

    $this->artisan('notifications:due-payments')->assertExitCode(0);

    Notification::assertSentTo($admin, PaymentDueNotification::class, function ($notification) {
        return str_contains($notification->ref, 'history-');
    });
});