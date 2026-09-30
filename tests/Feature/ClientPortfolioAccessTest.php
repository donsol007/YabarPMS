<?php

use App\Models\Client;
use App\Models\Portfolio;
use App\Models\User;

function portfolioAccessClient(array $overrides = []): Client
{
    return Client::factory()->create(array_merge([
        'portfolio_access_code' => '4821',
    ], $overrides));
}

function portfolioAccessClientPayload(Client $client, array $overrides = []): array
{
    return array_merge([
        'surname' => $client->surname,
        'first_name' => $client->first_name,
        'middle_name' => $client->middle_name,
        'sex' => $client->sex,
        'date_of_birth' => $client->date_of_birth->format('Y-m-d'),
        'mobile_number' => $client->mobile_number,
        'email' => $client->email,
    ], $overrides);
}

test('saving an access code generates a public link token', function () {
    $client = portfolioAccessClient(['portfolio_access_code' => 'secret99']);

    expect($client->portfolio_access_token)->not->toBeNull()
        ->and($client->hasPortfolioAccess())->toBeTrue()
        ->and($client->portfolioAccessUrl())->toContain($client->portfolio_access_token);
});

test('clearing the access code disables the public link', function () {
    $client = portfolioAccessClient();
    $token = $client->portfolio_access_token;

    $client->update(['portfolio_access_code' => null]);

    expect($client->refresh()->portfolio_access_token)->toBeNull()
        ->and($client->hasPortfolioAccess())->toBeFalse()
        ->and($client->portfolio_access_url)->toBeNull();
});

test('admin can assign an access code through the client form', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->put("/clients/{$client->id}", portfolioAccessClientPayload($client, [
            'portfolio_access_code' => 'YB-2026',
        ]))
        ->assertRedirect();

    $client->refresh();

    expect($client->portfolio_access_code)->toBe('YB-2026')
        ->and($client->portfolio_access_token)->not->toBeNull();
});

test('guests can open the access code page with a valid link', function () {
    $client = portfolioAccessClient();

    $this->get(route('portfolio.access.show', $client->portfolio_access_token))
        ->assertOk()
        ->assertSee('Secure Portfolio Access');
});

test('an unknown portfolio link returns not found', function () {
    $this->get('/portfolio-access/this-token-does-not-exist')->assertNotFound();
});

test('a client without an access code has no reachable link', function () {
    $client = Client::factory()->create();

    expect($client->hasPortfolioAccess())->toBeFalse();

    $this->get('/portfolio-access/'.str_repeat('a', 48))->assertNotFound();
});

test('an incorrect access code is rejected', function () {
    $client = portfolioAccessClient(['portfolio_access_code' => '4821']);

    $this->post(route('portfolio.access.unlock', $client->portfolio_access_token), [
        'access_code' => '9999',
    ])->assertSessionHasErrors('access_code');

    $this->get(route('portfolio.access.view', $client->portfolio_access_token))
        ->assertRedirect(route('portfolio.access.show', $client->portfolio_access_token));
});

test('a correct access code unlocks the portfolio details', function () {
    $client = portfolioAccessClient(['portfolio_access_code' => '4821']);
    Portfolio::factory()->withInstruments()->create(['client_id' => $client->id]);

    $this->post(route('portfolio.access.unlock', $client->portfolio_access_token), [
        'access_code' => '4821',
    ])->assertRedirect(route('portfolio.access.view', $client->portfolio_access_token));

    $this->get(route('portfolio.access.view', $client->portfolio_access_token))
        ->assertOk()
        ->assertSee($client->full_name)
        ->assertSee('Total Portfolio Value');
});

test('portfolio details cannot be viewed without unlocking first', function () {
    $client = portfolioAccessClient();

    $this->get(route('portfolio.access.view', $client->portfolio_access_token))
        ->assertRedirect(route('portfolio.access.show', $client->portfolio_access_token));
});

test('admin can regenerate the link which invalidates the previous one', function () {
    $user = adminUser();
    $client = portfolioAccessClient();
    $oldToken = $client->portfolio_access_token;

    $this->actingAs($user)
        ->post(route('clients.portfolio-access.regenerate', $client))
        ->assertRedirect();

    $client->refresh();

    expect($client->portfolio_access_token)->not->toBe($oldToken);

    $this->get(route('portfolio.access.show', $oldToken))->assertNotFound();
});

test('a user without edit clients permission cannot regenerate the link', function () {
    $user = User::factory()->create();
    $client = portfolioAccessClient();
    $token = $client->portfolio_access_token;

    $this->actingAs($user)
        ->post(route('clients.portfolio-access.regenerate', $client))
        ->assertForbidden();

    expect($client->refresh()->portfolio_access_token)->toBe($token);
});
