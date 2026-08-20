<?php

use App\Models\User;
use App\Notifications\StaffCreatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('admin can create a staff account', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/staff', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'phone' => '08011111111',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'staff',
        ])
        ->assertRedirect('/staff');

    $staff = User::where('email', 'newstaff@example.com')->first();

    expect($staff)->not->toBeNull()
        ->and($staff->hasRole('staff'))->toBeTrue()
        ->and($staff->phone)->toBe('08011111111');
});

test('duplicate staff email is rejected', function () {
    $user = adminUser();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user)
        ->post('/staff', [
            'name' => 'New Staff',
            'email' => 'taken@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])
        ->assertSessionHasErrors('email');
});

test('weak staff password is rejected', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->post('/staff', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertSessionHasErrors('password');
});

test('staff are notified when an account is created', function () {
    Notification::fake();

    $admin = adminUser();

    $this->actingAs($admin)
        ->post('/staff', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

    $staff = User::where('email', 'newstaff@example.com')->first();

    Notification::assertSentTo([$admin], StaffCreatedNotification::class, function ($notification) use ($staff) {
        return $notification->user->id === $staff->id;
    });
});

test('staff user can log in with their assigned credentials', function () {
    $admin = adminUser();

    $this->actingAs($admin)->post('/staff', [
        'name' => 'Loggable Staff',
        'email' => 'loggable@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $this->post('/logout');
    $this->assertGuest();

    $component = \Livewire\Volt\Volt::test('pages.auth.login')
        ->set('form.email', 'loggable@example.com')
        ->set('form.password', 'Password123');

    $component->call('login');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('admin can edit a staff account', function () {
    $admin = adminUser();
    $staff = User::factory()->create();
    $staff->assignRole('staff');

    $this->actingAs($admin)
        ->put("/staff/{$staff->id}", [
            'name' => 'Renamed Staff',
            'email' => 'renamed@example.com',
            'phone' => '08022222222',
            'role' => 'admin',
        ])
        ->assertRedirect('/staff');

    $staff->refresh();

    expect($staff->name)->toBe('Renamed Staff')
        ->and($staff->email)->toBe('renamed@example.com')
        ->and($staff->phone)->toBe('08022222222')
        ->and($staff->hasRole('admin'))->toBeTrue()
        ->and($staff->hasRole('staff'))->toBeFalse();
});

test('admin can reset a staff password on update', function () {
    $admin = adminUser();
    $staff = User::factory()->create(['password' => 'OldPass123']);

    $this->actingAs($admin)
        ->put("/staff/{$staff->id}", [
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => 'NewPass456',
            'password_confirmation' => 'NewPass456',
        ])
        ->assertRedirect('/staff');

    $staff->refresh();

    expect(Hash::check('NewPass456', $staff->password))->toBeTrue();
});

test('blank password on update keeps the current password', function () {
    $admin = adminUser();
    $staff = User::factory()->create(['password' => 'KeepMe123']);

    $this->actingAs($admin)
        ->put("/staff/{$staff->id}", [
            'name' => $staff->name,
            'email' => $staff->email,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect('/staff');

    $staff->refresh();

    expect(Hash::check('KeepMe123', $staff->password))->toBeTrue();
});

test('duplicate email is rejected when editing staff', function () {
    $admin = adminUser();
    $staff = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->put("/staff/{$staff->id}", [
            'name' => $staff->name,
            'email' => 'taken@example.com',
        ])
        ->assertSessionHasErrors('email');
});

test('admin can delete a staff account', function () {
    $admin = adminUser();
    $staff = User::factory()->create();

    $this->actingAs($admin)
        ->delete("/staff/{$staff->id}")
        ->assertRedirect('/staff');

    expect(User::find($staff->id))->toBeNull();
});

test('admin cannot delete their own account', function () {
    $admin = adminUser();
    $other = User::factory()->create();

    $this->actingAs($admin)
        ->delete("/staff/{$admin->id}")
        ->assertRedirect()
        ->assertSessionHas('toast.type', 'error');

    expect(User::find($admin->id))->not->toBeNull()
        ->and(User::find($other->id))->not->toBeNull();
});

test('staff cannot edit, update, or delete staff accounts', function () {
    $staff = staffUser();
    $target = User::factory()->create();

    $this->actingAs($staff)
        ->get("/staff/{$target->id}/edit")
        ->assertForbidden();

    $this->actingAs($staff)
        ->put("/staff/{$target->id}", ['name' => 'x', 'email' => $target->email])
        ->assertForbidden();

    $this->actingAs($staff)
        ->delete("/staff/{$target->id}")
        ->assertForbidden();
});