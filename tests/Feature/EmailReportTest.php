<?php

use App\Mail\ClientPortfolioReport;
use App\Models\Client;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('report is emailed to the client when mail is configured', function () {
    Mail::fake();

    $user = adminUser();
    $portfolio = Portfolio::factory()->withInstruments()->create();

    Setting::set('mail_host', 'smtp.gmail.com');
    Setting::set('mail_port', 587);
    Setting::set('mail_username', 'reports@example.com');
    Setting::set('mail_password', 'secret');
    Setting::set('mail_encryption', 'tls');
    Setting::set('mail_from_address', 'reports@example.com');
    Setting::set('mail_from_name', 'Yabar Reports');

    $this->actingAs($user)
        ->post('/reports/portfolio/email', ['portfolio_id' => $portfolio->id])
        ->assertRedirect();

    $client = $portfolio->client;

    Mail::assertSent(ClientPortfolioReport::class, function ($mail) use ($client) {
        return $mail->hasTo($client->email)
            && count($mail->attachments()) === 1;
    });
});

test('report is not emailed when mail settings are missing', function () {
    Mail::fake();

    $user = adminUser();
    $portfolio = Portfolio::factory()->create();

    Setting::forget('mail_host');
    Setting::forget('mail_from_address');

    $this->actingAs($user)
        ->post('/reports/portfolio/email', ['portfolio_id' => $portfolio->id])
        ->assertRedirect();

    Mail::assertNothingSent();
});

test('report is not emailed when the client has no email address', function () {
    Mail::fake();

    $user = adminUser();
    $client = Client::factory()->create(['email' => '']);
    $portfolio = Portfolio::factory()->create(['client_id' => $client->id]);

    Setting::set('mail_host', 'smtp.gmail.com');
    Setting::set('mail_from_address', 'reports@example.com');

    $this->actingAs($user)
        ->post('/reports/portfolio/email', ['portfolio_id' => $portfolio->id])
        ->assertRedirect();

    Mail::assertNothingSent();
});

test('a user without report permission cannot email a report', function () {
    Mail::fake();

    $user = App\Models\User::factory()->create();
    $portfolio = Portfolio::factory()->create();

    $this->actingAs($user)
        ->post('/reports/portfolio/email', ['portfolio_id' => $portfolio->id])
        ->assertForbidden();

    Mail::assertNothingSent();
});

test('staff with report permission can email a report', function () {
    Mail::fake();

    $user = staffUser();
    $portfolio = Portfolio::factory()->create();

    Setting::set('mail_host', 'smtp.gmail.com');
    Setting::set('mail_from_address', 'reports@example.com');

    $this->actingAs($user)
        ->post('/reports/portfolio/email', ['portfolio_id' => $portfolio->id])
        ->assertRedirect();

    Mail::assertSent(ClientPortfolioReport::class);
});
