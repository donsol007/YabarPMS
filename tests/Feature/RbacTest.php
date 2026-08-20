<?php

use App\Models\Client;
use App\Models\Portfolio;

test('guests are redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/clients')->assertRedirect('/login');
    $this->get('/reports')->assertRedirect('/login');
});

test('staff cannot access admin-only portfolio mutations', function () {
    $user = staffUser();
    $portfolio = Portfolio::factory()->create();

    $this->actingAs($user)
        ->get('/portfolios/create')
        ->assertForbidden();

    $this->actingAs($user)
        ->delete("/portfolios/{$portfolio->id}")
        ->assertForbidden();

    $this->actingAs($user)
        ->get("/portfolios/{$portfolio->id}/edit")
        ->assertForbidden();
});

test('staff cannot manage staff accounts or settings', function () {
    $user = staffUser();

    $this->actingAs($user)
        ->get('/staff')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/staff/create')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/settings')
        ->assertForbidden();
});

test('staff cannot delete clients', function () {
    $user = staffUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->delete("/clients/{$client->id}")
        ->assertForbidden();

    expect(Client::find($client->id))->not->toBeNull();
});

test('staff can view and create clients', function () {
    $user = staffUser();

    $this->actingAs($user)
        ->get('/clients')
        ->assertOk();

    $this->actingAs($user)
        ->get('/clients/create')
        ->assertOk();
});

test('staff can view portfolios and reports', function () {
    $user = staffUser();
    $portfolio = Portfolio::factory()->create();

    $this->actingAs($user)
        ->get('/portfolios')
        ->assertOk();

    $this->actingAs($user)
        ->get("/portfolios/{$portfolio->id}")
        ->assertOk();

    $this->actingAs($user)
        ->get('/reports')
        ->assertOk();
});

test('admin can access all admin-only sections', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->get('/staff')
        ->assertOk();

    $this->actingAs($user)
        ->get('/staff/create')
        ->assertOk();

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk();
});