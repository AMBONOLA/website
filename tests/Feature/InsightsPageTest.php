<?php

use App\Models\Insight;
use Illuminate\Support\Facades\DB;

it('lists published insights but not drafts', function () {
    Insight::factory()->published()->create(['title' => 'A published insight']);
    Insight::factory()->create(['title' => 'A draft insight']);

    $response = $this->get(route('insights.index'));

    $response->assertOk()
        ->assertSee('A published insight')
        ->assertDontSee('A draft insight');
});

it('does not list insights scheduled for the future', function () {
    Insight::factory()->create(['title' => 'A future insight', 'published_at' => now()->addDay()]);

    $response = $this->get(route('insights.index'));

    $response->assertDontSee('A future insight');
});

it('shows a message when nothing is published yet', function () {
    $response = $this->get(route('insights.index'));

    $response->assertOk()->assertSee('Nothing here yet.');
});

it('renders a published insight with its formatting', function () {
    $insight = Insight::factory()->published()->create([
        'title' => 'Measure before you optimise',
        'body' => '<h2 style="text-align: center">What I measured</h2>'
            .'<p>It was <strong>the file share</strong>, '
            .'<span style="font-family: Georgia, serif; font-size: 24px; color: #d19c12; background-color: #faf0c4">really</span>.</p>'
            .'<p><a href="https://laravel.com" target="_blank" rel="noopener noreferrer nofollow">Laravel</a></p>',
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertSee('Measure before you optimise')
        ->assertSee('<h2 style="text-align:center;">What I measured</h2>', escape: false)
        ->assertSee('<strong>the file share</strong>', escape: false)
        ->assertSee('font-size:24px;', escape: false)
        ->assertSee('color:#d19c12;', escape: false)
        ->assertSee('background-color:#faf0c4;', escape: false)
        ->assertSee('Georgia', escape: false)
        ->assertSee('href="https://laravel.com"', escape: false);
});

it('returns 404 for a draft insight', function () {
    $insight = Insight::factory()->create();

    $response = $this->get(route('insights.show', $insight));

    $response->assertNotFound();
});

it('strips scripts, event handlers and unsafe links from the insight body', function () {
    $insight = Insight::factory()->published()->create([
        'body' => "<script>alert('xss')</script>"
            .'<p onmouseover="alert(1)">Hover</p>'
            .'<p><a href="javascript:alert(1)">click me</a></p>'
            .'<iframe src="https://evil.example"></iframe>',
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertSee('Hover')
        ->assertDontSee("<script>alert('xss')</script>", escape: false)
        ->assertDontSee('onmouseover', escape: false)
        ->assertDontSee('javascript:', escape: false)
        ->assertDontSee('evil.example', escape: false);
});

it('strips CSS that could restyle or overlay the page', function () {
    $insight = Insight::factory()->published()->create([
        'body' => '<p style="position: fixed; top: 0; background-image: url(https://evil.example/x.png); color: red">Styled</p>',
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertSee('color:#FF0000;', escape: false)
        ->assertDontSee('fixed', escape: false)
        ->assertDontSee('evil.example', escape: false);
});

it('sanitizes a body that was written to the database directly', function () {
    $insight = Insight::factory()->published()->create();
    DB::table('insights')->where('id', $insight->id)->update([
        'body' => '<p>Tampered</p><script>alert(1)</script><img src=x onerror="alert(1)">',
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertSee('<p>Tampered</p>', escape: false)
        ->assertDontSee('<script>alert(1)</script>', escape: false)
        ->assertDontSee('onerror', escape: false);
});

it('shows the reading time rounded up to whole minutes', function (int $word_count, string $expected) {
    $insight = Insight::factory()->published()->create([
        'body' => '<h2>Title</h2><p>'.implode(' ', array_fill(0, $word_count - 1, 'word')).'</p>',
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertSee($expected);
})->with([
    'short post' => [12, '1 min read'],
    'just over two minutes' => [401, '3 min read'],
]);
