<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('the admin can authenticate using the login screen', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('the admin can authenticate with differently cased email', function () {
    $admin = User::factory()->admin()->create();

    $this->post('/admin/login', [
        'email' => 'ADMIN@Example.com',
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($admin);
});

test('the admin can not authenticate with invalid password', function () {
    $admin = User::factory()->admin()->create();

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users other than the admin can not authenticate even with the correct password', function () {
    $user = User::factory()->create();

    $response = $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email' => trans('auth.failed')]);
});

test('nobody can authenticate when no admin email is configured', function () {
    $admin = User::factory()->admin()->create();
    config(['auth.admin_email' => null]);

    $this->post('/admin/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('login attempts from one ip are limited across different emails', function () {
    foreach (range(1, 10) as $attempt) {
        $this->post('/admin/login', [
            'email' => "attacker{$attempt}@example.com",
            'password' => 'password',
        ])->assertRedirect();
    }

    $this->post('/admin/login', [
        'email' => 'attacker11@example.com',
        'password' => 'password',
    ])->assertTooManyRequests();
});

test('the admin can logout', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post('/admin/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login', absolute: false));
});
