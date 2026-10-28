<?php

use App\Models\User;

test('the user with the configured admin email is the admin', function () {
    $admin = User::factory()->make(['email' => 'Admin@Example.com']);

    expect($admin->isAdmin())->toBeTrue();
});

test('a user with any other email is not the admin', function () {
    $user = User::factory()->make(['email' => 'someone@example.com']);

    expect($user->isAdmin())->toBeFalse();
});

test('nobody is the admin when no admin email is configured', function (?string $admin_email) {
    config(['auth.admin_email' => $admin_email]);

    $user = User::factory()->make(['email' => '']);

    expect($user->isAdmin())->toBeFalse();
})->with([null, '']);
