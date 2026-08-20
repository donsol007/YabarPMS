<?php

use App\Models\Client;
use App\Models\Portfolio;
use App\Models\User;

test('client full name concatenates names in order', function () {
    $client = new Client([
        'first_name' => 'John',
        'middle_name' => 'Obi',
        'surname' => 'Doe',
    ]);

    expect($client->full_name)->toBe('John Obi Doe');
});

test('client full name tolerates missing names', function () {
    $client = new Client([
        'first_name' => 'John',
        'middle_name' => '',
        'surname' => 'Doe',
    ]);

    expect($client->full_name)->toBe('John Doe');
});

test('next client id starts at one for an empty table', function () {
    expect(Client::nextClientId())->toBe('YFC-0001');
});

test('user admin and staff role checks', function () {
    $user = new User();

    expect($user->isAdmin())->toBeFalse()
        ->and($user->isStaff())->toBeFalse();
});

test('user initials derive from first and last name', function () {
    $user = new User(['name' => 'Jane Doe']);

    expect($user->initials())->toBe('JD');
});

test('format money uses the currency symbol', function () {
    expect(format_money(1250000))->toBe('₦1,250,000.00');
});

test('format date handles null and dates', function () {
    expect(format_date(null))->toBe('—')
        ->and(format_date('2026-01-05'))->toBe('05/01/2026');
});

test('portfolio total methods handle empty portfolios', function () {
    $portfolio = Portfolio::factory()->create();

    expect($portfolio->total_fixed_debt)->toBe(0.0)
        ->and($portfolio->total_equity_value)->toBe(0.0)
        ->and($portfolio->total_portfolio_value)->toBe(0.0);
});