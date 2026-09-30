<?php

use App\Models\Client;
use App\Models\State;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validClientPayload(array $overrides = []): array
{
    $state = State::factory()->create();

    return array_merge([
        'surname' => 'Doe',
        'first_name' => 'John',
        'middle_name' => 'Obi',
        'sex' => 'Male',
        'date_of_birth' => '1990-01-15',
        'mobile_number' => '08012345678',
        'mother_maiden_name' => 'Okafor',
        'residential_address' => '12 Marina, Lagos',
        'state_of_origin_id' => $state->id,
        'lga_name' => 'Ikeja',
        'marital_status' => 'Married',
        'religion' => 'Christianity',
        'email' => 'john.doe@example.com',
        'amount_to_invest' => 2500000,
        'bank_name' => 'Guaranty Trust Bank',
        'account_name' => 'John Obi Doe',
        'account_number' => '0123456789',
        'account_type' => 'Savings',
        'bvn' => '22123456789',
        'account_opening_date' => '2024-01-10',
        'bank_address' => 'Broad Street, Lagos',
        'occupation' => 'Software Engineer',
        'employer_name' => 'Yabar Tech',
        'employer_address' => 'Ikoyi, Lagos',
        'hobbies' => 'Reading',
        'next_of_kin' => [
            'name' => 'Mary Doe',
            'address' => 'Surulere, Lagos',
            'phone' => '08098765432',
            'relationship' => 'Spouse',
            'email' => 'mary.doe@example.com',
        ],
    ], $overrides);
}

test('admin can register a client with next of kin and documents', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload())
        ->assertRedirect();

    $client = Client::where('email', 'john.doe@example.com')->first();

    expect($client)->not->toBeNull()
        ->and($client->client_id)->toBe('YFC-0001')
        ->and($client->full_name)->toBe('John Obi Doe')
        ->and($client->nextOfKin)->not->toBeNull()
        ->and($client->nextOfKin->name)->toBe('Mary Doe')
        ->and($client->created_by)->toBe($user->id);
});

test('client documents are uploaded and stored', function () {
    Storage::fake('public');
    $user = adminUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload([
            'documents' => [
                'id_card' => UploadedFile::fake()->image('id.jpg'),
                'utility_bill' => UploadedFile::fake()->create('bill.pdf', 100, 'application/pdf'),
                'signature' => UploadedFile::fake()->image('signature.png'),
            ],
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $client = Client::where('email', 'john.doe@example.com')->first();

    expect($client->documents)->toHaveCount(3);

    foreach (['id_card', 'utility_bill', 'signature'] as $type) {
        $document = $client->documents->firstWhere('type', $type);

        expect($document)->not->toBeNull()
            ->and(Storage::disk('public')->exists($document->stored_path))->toBeTrue();
    }
});

test('admin can register a client with a blank amount to invest', function () {
    $user = adminUser();
    $payload = validClientPayload();
    unset($payload['amount_to_invest']);

    $this->actingAs($user)
        ->post('/clients', $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $client = Client::where('email', 'john.doe@example.com')->first();

    expect($client)->not->toBeNull()
        ->and((float) $client->amount_to_invest)->toBe(0.0);
});

test('staff can register a client with a blank amount to invest', function () {
    $user = staffUser();
    $payload = validClientPayload();
    unset($payload['amount_to_invest']);

    $this->actingAs($user)
        ->post('/clients', $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect(Client::where('email', 'john.doe@example.com')->exists())->toBeTrue();
});

test('client id increments sequentially', function () {
    adminUser();
    Client::factory()->create(['client_id' => 'YFC-0005']);

    expect(Client::nextClientId())->toBe('YFC-0006');
});

test('client id is preserved after soft delete', function () {
    adminUser();
    $client = Client::factory()->create();
    $client->delete();

    $new = Client::factory()->create();

    expect($new->client_id)->not->toBe($client->client_id);
});

test('staff can register a client', function () {
    $user = staffUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload())
        ->assertRedirect();

    expect(Client::where('email', 'john.doe@example.com')->exists())->toBeTrue();
});

test('client validation rejects an invalid mobile number', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload(['mobile_number' => '12345']))
        ->assertSessionHasErrors('mobile_number');
});

test('client validation rejects invalid BVN and account number lengths', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload([
            'bvn' => '123',
            'account_number' => '456',
        ]))
        ->assertSessionHasErrors(['bvn', 'account_number']);
});

test('client validation requires required fields', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/clients', validClientPayload(['surname' => '', 'email' => 'not-an-email']))
        ->assertSessionHasErrors(['surname', 'email']);
});

test('client email must be unique', function () {
    $user = adminUser();
    Client::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user)
        ->post('/clients', validClientPayload(['email' => 'taken@example.com']))
        ->assertSessionHasErrors('email');
});

test('admin can update a client', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->put("/clients/{$client->id}", validClientPayload([
            'surname' => 'Updated',
            'email' => 'updated@example.com',
        ]))
        ->assertRedirect();

    expect($client->refresh()->surname)->toBe('Updated')
        ->and($client->email)->toBe('updated@example.com');
});

test('admin can soft delete a client', function () {
    $user = adminUser();
    $client = Client::factory()->create();

    $this->actingAs($user)
        ->delete("/clients/{$client->id}")
        ->assertRedirect('/clients');

    expect(Client::find($client->id))->toBeNull()
        ->and(Client::withTrashed()->find($client->id))->not->toBeNull();
});

test('client document download requires ownership', function () {
    $user = adminUser();
    $client = Client::factory()->create();
    $other = Client::factory()->create();

    $document = $client->documents()->create([
        'type' => 'id_card',
        'original_name' => 'photo.jpg',
        'stored_path' => 'documents/1/photo.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);

    $this->actingAs($user)
        ->get("/clients/{$other->id}/documents/{$document->id}/download")
        ->assertNotFound();
});
