<?php

use App\Models\User;
use Illuminate\Support\Facades\Vite;

it('sends a script policy that only allows our own and nonce-tagged scripts', function (string $url) {
    $response = $this->get($url);

    $policy = $response->headers->get('Content-Security-Policy');
    expect($policy)
        ->toContain("default-src 'self'")
        ->toMatch("/script-src 'self' 'nonce-[A-Za-z0-9]+'/")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'none'")
        ->not->toContain("'unsafe-eval'")
        ->not->toMatch("/script-src[^;]*'unsafe-inline'/");
    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
})->with([
    'home' => '/',
    'admin login' => '/admin/login',
]);

it('tags the inline route script with the policy nonce', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    preg_match("/'nonce-([A-Za-z0-9]+)'/", $response->headers->get('Content-Security-Policy'), $matches);
    $response->assertSee('<script type="text/javascript" nonce="'.$matches[1].'">', escape: false);
});

it('only relaxes the policy for the Vite dev server when running locally', function (string $environment, bool $is_relaxed) {
    app()['env'] = $environment;
    Vite::useHotFile(tap(tempnam(sys_get_temp_dir(), 'hot'), fn (string $path) => file_put_contents($path, 'http://localhost:5173')));

    $response = $this->get('/');

    expect(str_contains($response->headers->get('Content-Security-Policy'), ' http: ws:'))->toBe($is_relaxed);
})->with([
    'local' => ['local', true],
    'production' => ['production', false],
]);

it('sends HSTS only over HTTPS', function (string $url, ?string $expected) {
    $response = $this->get($url);

    expect($response->headers->get('Strict-Transport-Security'))->toBe($expected);
})->with([
    'https' => ['https://localhost/', 'max-age=31536000'],
    'http' => ['http://localhost/', null],
]);
