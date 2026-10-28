<?php

use App\Models\Insight;

it('renders the portfolio home page as a server-rendered blade view', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertViewIs('home')
        ->assertSee('Andrea Bonola')
        ->assertSee('Full-Stack Engineer')
        ->assertDontSee('data-page=', escape: false);
});

it('shows the three newest published insights', function () {
    Insight::factory()->published()->count(4)->sequence(
        ['title' => 'Oldest insight', 'published_at' => now()->subDays(4)],
        ['title' => 'Third newest insight', 'published_at' => now()->subDays(3)],
        ['title' => 'Second newest insight', 'published_at' => now()->subDays(2)],
        ['title' => 'Newest insight', 'published_at' => now()->subDay()],
    )->create();
    Insight::factory()->create(['title' => 'Draft insight']);

    $response = $this->get(route('home'));

    $response->assertSeeInOrder(['Newest insight', 'Second newest insight', 'Third newest insight'])
        ->assertDontSee('Oldest insight')
        ->assertDontSee('Draft insight');
});

it('hides the latest insights section when nothing is published', function () {
    $response = $this->get(route('home'));

    $response->assertDontSee('Latest insights');
});
