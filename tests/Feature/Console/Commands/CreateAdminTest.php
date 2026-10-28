<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates the admin account for the configured admin email', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', 'Andrea')
        ->expectsQuestion('Password', 'a-long-secret-password')
        ->expectsQuestion('Confirm password', 'a-long-secret-password')
        ->expectsOutputToContain('Admin account created for admin@example.com.')
        ->assertSuccessful();

    $admin = User::where('email', 'admin@example.com')->sole();

    expect($admin->name)->toBe('Andrea')
        ->and(Hash::check('a-long-secret-password', $admin->password))->toBeTrue();
});

it('resets the password of an existing admin and invalidates remember me cookies', function () {
    $admin = User::factory()->admin()->create(['remember_token' => 'old-token']);

    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', $admin->name)
        ->expectsQuestion('Password', 'a-brand-new-password')
        ->expectsQuestion('Confirm password', 'a-brand-new-password')
        ->expectsOutputToContain('Password reset for admin@example.com.')
        ->assertSuccessful();

    $admin->refresh();

    expect(User::count())->toBe(1)
        ->and(Hash::check('a-brand-new-password', $admin->password))->toBeTrue()
        ->and($admin->remember_token)->not->toBe('old-token');
});

it('rejects a password shorter than 12 characters', function () {
    // Outside tests the prompt asks again; under test Laravel stops at the first validation error.
    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', 'Andrea')
        ->expectsQuestion('Password', 'too-short')
        ->expectsOutputToContain('The password must be at least 12 characters.')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('fails without changes when no admin email is configured', function () {
    config(['auth.admin_email' => null]);

    $this->artisan('app:create-admin')
        ->expectsOutputToContain('ADMIN_EMAIL is not set.')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('fails without changes when the password confirmation does not match', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', 'Andrea')
        ->expectsQuestion('Password', 'a-long-secret-password')
        ->expectsQuestion('Confirm password', 'a-different-password')
        ->expectsOutputToContain('The passwords do not match.')
        ->assertFailed();

    expect(User::count())->toBe(0);
});
