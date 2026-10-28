<?php

use App\Models\User;

test('profile page is displayed', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->get('/admin/profile');

    $response->assertOk();
});

test('profile name can be updated', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->patch('/admin/profile', [
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/admin/profile');

    $this->assertSame('Test User', $admin->refresh()->name);
});

test('profile email can not be changed', function () {
    $admin = User::factory()->admin()->create();

    $this
        ->actingAs($admin)
        ->patch('/admin/profile', [
            'name' => 'Test User',
            'email' => 'attacker@example.com',
        ]);

    $this->assertSame('admin@example.com', $admin->refresh()->email);
});
