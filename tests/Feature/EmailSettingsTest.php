<?php

use App\Models\Setting;

test('admin can access the email settings page', function () {
    $user = adminUser();

    Setting::set('mail_host', 'smtp.example.com');
    Setting::set('mail_from_address', 'reports@example.com');

    $this->actingAs($user)
        ->get('/settings/email')
        ->assertOk()
        ->assertSee('smtp.example.com')
        ->assertSee('reports@example.com');
});

test('staff cannot access the email settings page', function () {
    $user = staffUser();

    $this->actingAs($user)
        ->get('/settings/email')
        ->assertForbidden();
});

test('admin can update email settings', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->put('/settings/email', [
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => 'reports@example.com',
            'mail_password' => 'secret',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'reports@example.com',
            'mail_from_name' => 'Yabar Reports',
        ])
        ->assertRedirect();

    expect(setting('mail_host'))->toBe('smtp.gmail.com')
        ->and((int) setting('mail_port'))->toBe(587)
        ->and(setting('mail_username'))->toBe('reports@example.com')
        ->and(setting('mail_password'))->toBe('secret')
        ->and(setting('mail_encryption'))->toBe('tls')
        ->and(setting('mail_from_address'))->toBe('reports@example.com')
        ->and(setting('mail_from_name'))->toBe('Yabar Reports');
});

test('an empty password keeps the current password', function () {
    $user = adminUser();

    Setting::set('mail_password', 'old-secret');

    $this->actingAs($user)
        ->put('/settings/email', [
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'ssl',
            'mail_from_address' => 'reports@example.com',
            'mail_from_name' => '',
        ])
        ->assertRedirect();

    expect(setting('mail_password'))->toBe('old-secret');
});

test('email settings validation rejects invalid values', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->put('/settings/email', [
            'mail_host' => '',
            'mail_port' => 0,
            'mail_encryption' => 'invalid',
            'mail_from_address' => 'not-an-email',
        ])
        ->assertSessionHasErrors(['mail_host', 'mail_port', 'mail_encryption', 'mail_from_address']);
});
