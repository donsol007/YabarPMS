<?php

use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

function guestRegistrationPayload(array $overrides = []): array
{
    return array_merge([
        'surname' => 'Doe',
        'first_name' => 'Jane',
        'middle_name' => 'Obi',
        'sex' => 'Female',
        'date_of_birth' => '1992-05-10',
        'mobile_number' => '08011112222',
        'email' => 'jane.doe@example.com',
    ], $overrides);
}

test('guests can access the client registration page', function () {
    $this->get('/client/register')
        ->assertOk()
        ->assertSee('Register as a Client');
});

test('authenticated users are redirected away from the registration page', function () {
    $this->actingAs(adminUser())
        ->get('/client/register')
        ->assertStatus(302);
});

test('a guest can register themselves as a client', function () {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test('pages.auth.client-register')
        ->set('surname', 'Doe')
        ->set('first_name', 'Jane')
        ->set('middle_name', 'Obi')
        ->set('sex', 'Female')
        ->set('date_of_birth', '1992-05-10')
        ->set('mobile_number', '08011112222')
        ->set('email', 'jane.doe@example.com')
        ->set('amount_to_invest', 1000000)
        ->call('register')
        ->assertHasNoErrors()
        ->assertSee('Registration Successful');

    $client = Client::where('email', 'jane.doe@example.com')->first();

    expect($client)->not->toBeNull()
        ->and($client->client_id)->toBe('YFC-0001')
        ->and($client->created_by)->toBeNull()
        ->and($client->status)->toBe('pending')
        ->and($client->full_name)->toBe('Jane Obi Doe');

    Notification::assertSentTo($user, ClientRegisteredNotification::class);
});

test('a guest registration stores next of kin details', function () {
    Livewire::test('pages.auth.client-register')
        ->set('surname', 'Doe')
        ->set('first_name', 'Jane')
        ->set('middle_name', 'Obi')
        ->set('sex', 'Female')
        ->set('date_of_birth', '1992-05-10')
        ->set('mobile_number', '08011112222')
        ->set('email', 'jane.doe@example.com')
        ->set('next_of_kin.name', 'Mary Doe')
        ->set('next_of_kin.phone', '08099998888')
        ->set('next_of_kin.relationship', 'Sibling')
        ->call('register')
        ->assertHasNoErrors();

    $client = Client::where('email', 'jane.doe@example.com')->first();

    expect($client->nextOfKin)->not->toBeNull()
        ->and($client->nextOfKin->name)->toBe('Mary Doe')
        ->and($client->nextOfKin->relationship)->toBe('Sibling');
});

test('guest registration requires a unique email', function () {
    Client::factory()->create(['email' => 'taken@example.com']);

    Livewire::test('pages.auth.client-register')
        ->set('surname', 'Doe')
        ->set('first_name', 'Jane')
        ->set('middle_name', 'Obi')
        ->set('sex', 'Female')
        ->set('date_of_birth', '1992-05-10')
        ->set('mobile_number', '08011112222')
        ->set('email', 'taken@example.com')
        ->call('register')
        ->assertHasErrors(['email']);
});

test('guest registration validates required and format rules', function () {
    Livewire::test('pages.auth.client-register')
        ->set('surname', '')
        ->set('mobile_number', '12345')
        ->set('email', 'not-an-email')
        ->call('register')
        ->assertHasErrors(['surname', 'first_name', 'middle_name', 'sex', 'date_of_birth', 'mobile_number', 'email']);
});

test('admin and staff can see the share registration link on the clients page', function () {
    $this->actingAs(adminUser())->get('/clients')->assertOk()->assertSee('Share Link');
    $this->actingAs(staffUser())->get('/clients')->assertOk()->assertSee('Share Link');
});

test('admin can approve a pending client registration', function () {
    $user = adminUser();
    $client = Client::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->post("/clients/{$client->id}/approve")
        ->assertRedirect('/clients');

    expect($client->refresh()->status)->toBe('approved');
});

test('staff can approve a pending client registration', function () {
    $user = staffUser();
    $client = Client::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->post("/clients/{$client->id}/approve")
        ->assertRedirect('/clients');

    expect($client->refresh()->status)->toBe('approved');
});

test('a user without edit clients permission cannot approve a client', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['status' => 'pending']);

    $this->actingAs($user)
        ->post("/clients/{$client->id}/approve")
        ->assertForbidden();
});