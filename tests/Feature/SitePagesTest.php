<?php

it('renders the about page', function () {
    $response = $this->get(route('about'));

    $response->assertOk()->assertSee('About me');
});

it('lists projects with their screenshot and links', function () {
    $response = $this->get(route('projects'));

    $response->assertSee('This website')
        ->assertSee(asset('images/projects/website.png'))
        ->assertSee('https://github.com/AMBONOLA/website');
});

it('links to every section from the navigation', function () {
    $response = $this->get(route('home'));

    $response->assertSee(route('insights.index'))
        ->assertSee(route('projects'))
        ->assertSee(route('about'));
});
