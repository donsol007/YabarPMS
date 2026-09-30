<?php

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('settings page shows current settings', function () {
    $user = adminUser();

    Setting::set('company_name', 'Test Company');

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk()
        ->assertSee('Test Company');
});

test('admin can update settings', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => 'Yabar Investment Co',
            'currency_symbol' => '₦',
            'due_notice_days' => 10,
            'upload_max_size' => 8,
        ])
        ->assertRedirect();

    expect(setting('company_name'))->toBe('Yabar Investment Co')
        ->and((int) setting('due_notice_days'))->toBe(10)
        ->and((int) setting('upload_max_size'))->toBe(8);
});

test('settings validation rejects invalid values', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => '',
            'currency_symbol' => '',
            'due_notice_days' => 0,
            'upload_max_size' => -1,
        ])
        ->assertSessionHasErrors(['company_name', 'currency_symbol', 'due_notice_days', 'upload_max_size']);
});

test('admin can upload a company logo', function () {
    $user = adminUser();

    Storage::fake('public');

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => 'Yabar Finance Consult Limited',
            'currency_symbol' => '₦',
            'due_notice_days' => 7,
            'upload_max_size' => 5,
            'company_logo' => UploadedFile::fake()->image('logo.png', 128, 128),
        ])
        ->assertRedirect();

    $path = setting('company_logo');

    expect($path)->not->toBeNull()
        ->and(Storage::disk('public')->exists($path))->toBeTrue();
});

test('uploading a new logo replaces the old one', function () {
    $user = adminUser();

    Storage::fake('public');

    Setting::set('company_logo', 'branding/old-logo.png');
    Storage::disk('public')->put('branding/old-logo.png', 'old');

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => 'Yabar Finance Consult Limited',
            'currency_symbol' => '₦',
            'due_notice_days' => 7,
            'upload_max_size' => 5,
            'company_logo' => UploadedFile::fake()->image('new-logo.png', 128, 128),
        ])
        ->assertRedirect();

    expect(Storage::disk('public')->missing('branding/old-logo.png'))->toBeTrue()
        ->and(Storage::disk('public')->exists(setting('company_logo')))->toBeTrue();
});

test('a non-image logo upload is rejected', function () {
    $user = adminUser();

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => 'Yabar Finance Consult Limited',
            'currency_symbol' => '₦',
            'due_notice_days' => 7,
            'upload_max_size' => 5,
            'company_logo' => UploadedFile::fake()->create('logo.txt', 10),
        ])
        ->assertSessionHasErrors('company_logo');
});

test('admin can upload a report logo', function () {
    $user = adminUser();

    Storage::fake('public');

    $this->actingAs($user)
        ->put('/settings', [
            'company_name' => 'Yabar Finance Consult Limited',
            'currency_symbol' => '₦',
            'due_notice_days' => 7,
            'upload_max_size' => 5,
            'report_logo' => UploadedFile::fake()->image('report-logo.png', 256, 256),
        ])
        ->assertRedirect();

    $path = setting('report_logo');

    expect($path)->not->toBeNull()
        ->and(Storage::disk('public')->exists($path))->toBeTrue();
});

test('settings page shows the report logo upload', function () {
    $user = adminUser();

    Storage::fake('public');

    Setting::set('report_logo', 'branding/report.png');
    Storage::disk('public')->put('branding/report.png', 'logo');

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk()
        ->assertSee('Report Logo')
        ->assertSee('report.png');
});

test('setting default is returned when unset', function () {
    Setting::forget('missing_key');

    expect(setting('missing_key', 'fallback'))->toBe('fallback');
});
