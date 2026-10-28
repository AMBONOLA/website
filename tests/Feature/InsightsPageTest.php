<?php

use App\Models\Insight;

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

it('renders a published insight from Markdown', function () {
    $insight = Insight::factory()->published()->create([
        'title' => 'Measure before you optimise',
        'body' => "## What I measured\n\nIt was **the file share**.",
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertOk()
        ->assertSee('Measure before you optimise')
        ->assertSee('<h2>What I measured</h2>', escape: false)
        ->assertSee('<strong>the file share</strong>', escape: false);
});

it('returns 404 for a draft insight', function () {
    $insight = Insight::factory()->create();

    $response = $this->get(route('insights.show', $insight));

    $response->assertNotFound();
});

it('escapes raw HTML and drops unsafe links in the insight body', function () {
    $insight = Insight::factory()->published()->create([
        'body' => "<script>alert('xss')</script>\n\n[click me](javascript:alert('xss'))",
    ]);

    $response = $this->get(route('insights.show', $insight));

    $response->assertOk()
        ->assertSee('&lt;script&gt;', escape: false)
        ->assertDontSee("<script>alert('xss')</script>", escape: false)
        ->assertDontSee('href="javascript:', escape: false);
});
