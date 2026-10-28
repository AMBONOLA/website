<?php

it('renders the about page', function () {
    $response = $this->get(route('about'));

    $response->assertOk()->assertSee('About me');
});

it('renders the projects page as coming soon', function () {
    $response = $this->get(route('projects'));

    $response->assertOk()->assertSee('Projects are coming soon');
});

it('links to every section from the navigation', function () {
    $response = $this->get(route('home'));

    $response->assertSee(route('insights.index'))
        ->assertSee(route('projects'))
        ->assertSee(route('about'));
});
