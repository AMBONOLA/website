<?php

it('renders the portfolio home page as a server-rendered blade view', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertViewIs('home')
        ->assertSee('Andrea Bonola')
        ->assertSee('Full-Stack Engineer')
        ->assertDontSee('data-page=', escape: false);
});
