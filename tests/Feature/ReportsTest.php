<?php

use App\Models\Client;
use App\Models\Portfolio;

test('reports page lists portfolios and clients', function () {
    $user = adminUser();
    $client = Client::factory()->create();
    Portfolio::factory()->create(['client_id' => $client->id]);

    $this->actingAs($user)
        ->get('/reports')
        ->assertOk()
        ->assertSee($client->client_id);
});

test('portfolio report generates a pdf download', function () {
    $user = adminUser();
    $portfolio = Portfolio::factory()->withInstruments()->create();

    $response = $this->actingAs($user)
        ->post('/reports/portfolio', ['portfolio_id' => $portfolio->id])
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('Content-Disposition'))->toContain('attachment');
});

test('client report generates a pdf download', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)
        ->post('/reports/client', [
            'client_ids' => [$client->id],
            'fields' => ['client_id', 'surname', 'first_name', 'amount_to_invest'],
        ])
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('Content-Disposition'))->toContain('attachment');
});

test('client report rejects invalid fields', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->post('/reports/client', [
            'client_ids' => [$client->id],
            'fields' => ['not_a_field'],
        ])
        ->assertSessionHasErrors('fields.0');
});

test('client report requires at least one client and field', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/reports/client', ['client_ids' => [], 'fields' => []])
        ->assertSessionHasErrors(['client_ids', 'fields']);
});

test('client report field helper resolves relations', function () {
    $client = Client::factory()->create();

    expect($client->reportField('client_id'))->toBe($client->client_id)
        ->and($client->reportField('bank_name'))->toBe($client->bank?->name)
        ->and($client->reportField('missing_field'))->toBe('—');
});