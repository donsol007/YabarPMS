<?php

use App\Livewire\ClientIndex;
use App\Models\Client;
use App\Models\ClientRegistrationInvite;
use App\Models\User;
use App\Notifications\ClientRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
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

function registrationInvite(array $overrides = []): ClientRegistrationInvite
{
    return ClientRegistrationInvite::create(array_merge([
        'token' => Str::random(40),
        'expires_at' => now()->addDay(),
        'used_at' => null,
    ], $overrides));
}

function guestRegistrationComponent(ClientRegistrationInvite $invite)
{
    return Livewire::test('pages.auth.client-register', ['token' => $invite->token])
        ->set('surname', 'Doe')
        ->set('first_name', 'Jane')
        ->set('middle_name', 'Obi')
        ->set('sex', 'Female')
        ->set('date_of_birth', '1992-05-10')
        ->set('mobile_number', '08011112222')
        ->set('email', 'jane.doe@example.com');
}

test('guests can access the client registration page with a valid share link', function () {
    $invite = registrationInvite();

    $this->get("/client/register/{$invite->token}")
        ->assertOk()
        ->assertSee('Register as a Client');
});

test('guests cannot access the registration page without a valid share link', function () {
    $this->get('/client/register/invalid-token')
        ->assertOk()
        ->assertSee('Link Unavailable');
});

test('authenticated users are redirected away from the registration page', function () {
    $invite = registrationInvite();

    $this->actingAs(adminUser())
        ->get("/client/register/{$invite->token}")
        ->assertStatus(302);
});

test('a guest can register themselves as a client through a share link', function () {
    Notification::fake();
    $user = User::factory()->create();
    $invite = registrationInvite(['created_by' => $user->id]);

    guestRegistrationComponent($invite)
        ->set('amount_to_invest', 1000000)
        ->set('lga_name', 'Ikeja')
        ->call('register')
        ->assertHasNoErrors()
        ->assertSee('Registration Successful');

    $client = Client::where('email', 'jane.doe@example.com')->first();

    expect($client)->not->toBeNull()
        ->and($client->client_id)->toBe('YFC-0001')
        ->and($client->created_by)->toBeNull()
        ->and($client->status)->toBe('pending')
        ->and($client->full_name)->toBe('Jane Obi Doe')
        ->and($client->lga_name)->toBe('Ikeja');

    Notification::assertSentTo($user, ClientRegisteredNotification::class);

    $invite->refresh();

    expect($invite->isUsed())->toBeTrue();
});

test('a share link cannot be used to register more than once', function () {
    Notification::fake();
    $invite = registrationInvite();

    guestRegistrationComponent($invite)->call('register')->assertHasNoErrors();

    guestRegistrationComponent($invite)
        ->set('email', 'other@example.com')
        ->call('register')
        ->assertHasErrors(['token']);

    expect(Client::where('email', 'other@example.com')->exists())->toBeFalse();
});

test('an expired share link cannot be used to register', function () {
    Notification::fake();
    $invite = registrationInvite(['expires_at' => now()->subHour()]);

    guestRegistrationComponent($invite)
        ->call('register')
        ->assertHasErrors(['token']);

    expect(Client::where('email', 'jane.doe@example.com')->exists())->toBeFalse();
});

test('a used share link shows an unavailable notice when visited', function () {
    $invite = registrationInvite(['used_at' => now()]);

    $this->get("/client/register/{$invite->token}")
        ->assertOk()
        ->assertSee('Link Unavailable');
});

test('an expired share link shows an unavailable notice when visited', function () {
    $invite = registrationInvite(['expires_at' => now()->subDay()]);

    $this->get("/client/register/{$invite->token}")
        ->assertOk()
        ->assertSee('Link Unavailable');
});

test('a guest registration stores next of kin details', function () {
    Notification::fake();
    $invite = registrationInvite();

    guestRegistrationComponent($invite)
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
    Notification::fake();
    Client::factory()->create(['email' => 'taken@example.com']);
    $invite = registrationInvite();

    guestRegistrationComponent($invite)
        ->set('email', 'taken@example.com')
        ->call('register')
        ->assertHasErrors(['email']);
});

test('guest registration validates required and format rules', function () {
    Notification::fake();
    $invite = registrationInvite();

    Livewire::test('pages.auth.client-register', ['token' => $invite->token])
        ->set('surname', '')
        ->set('mobile_number', '12345')
        ->set('email', 'not-an-email')
        ->call('register')
        ->assertHasErrors(['surname', 'first_name', 'sex', 'date_of_birth', 'mobile_number', 'email']);
});

test('generating a share link creates a single-use invite valid for one day', function () {
    $this->actingAs(adminUser());

    Livewire::test(ClientIndex::class)
        ->call('generateShareLink');

    $invite = ClientRegistrationInvite::sole();

    expect($invite->created_by)->toBe(auth()->id())
        ->and($invite->isActive())->toBeTrue()
        ->and($invite->expires_at->format('Y-m-d H:i'))->toBe(now()->addDay()->format('Y-m-d H:i'));
});

test('admin and staff can see the share registration link button on the clients page', function () {
    $this->actingAs(adminUser())->get('/clients')->assertOk()->assertSee('Share Link');
    $this->actingAs(staffUser())->get('/clients')->assertOk()->assertSee('Share Link');
});

test('a user without create clients permission cannot generate a share link', function () {
    seedRoles();

    $this->actingAs(User::factory()->create());

    Livewire::test(ClientIndex::class)
        ->call('generateShareLink')
        ->assertForbidden();

    expect(ClientRegistrationInvite::count())->toBe(0);
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
