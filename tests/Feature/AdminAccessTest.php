<?php

use App\Models\User;

dataset('admin pages', [
    'dashboard' => '/admin',
    'profile' => '/admin/profile',
    'confirm password' => '/admin/confirm-password',
    'insights' => '/admin/insights',
    'new insight' => '/admin/insights/create',
]);

it('redirects guests to the admin login', function (string $url) {
    $response = $this->get($url);

    $response->assertRedirect(route('login'));
})->with('admin pages');

it('forbids logged-in users other than the admin', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertForbidden();
})->with('admin pages');

it('shows the admin dashboard to the admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
});

it('gives the admin the full route list', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertSee('admin.insights.store');
});

it('does not expose registration, password reset, or the old login path', function (string $url) {
    $response = $this->get($url);

    $response->assertNotFound();
})->with([
    '/register',
    '/forgot-password',
    '/reset-password/some-token',
    '/verify-email',
    '/login',
    '/dashboard',
]);
