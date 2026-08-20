<?php

use App\Models\Client;
use App\Models\Portfolio;

test('dashboard renders stats for an authenticated user', function () {
    $user = adminUser();
    $client = Client::factory()->create(['amount_to_invest' => 2500000]);
    Portfolio::factory()->withInstruments()->create(['client_id' => $client->id]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('1');
});

test('root route redirects authenticated users to dashboard data', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->get('/')
        ->assertOk();
});

test('dashboard handles empty data', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();
});