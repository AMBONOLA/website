<?php

use App\Models\Insight;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @return array<string, mixed>
 */
function insightPayload(array $overrides = []): array
{
    return [
        'title' => 'Docker on Windows is slow',
        'slug' => 'docker-on-windows-is-slow',
        'excerpt' => 'Why bind mounts hurt and what to do about it.',
        'body' => '## The problem',
        'is_published' => false,
        ...$overrides,
    ];
}

it('forbids logged-in users other than the admin from creating insights', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.insights.store'), insightPayload());

    $response->assertForbidden();
    expect(Insight::count())->toBe(0);
});

describe('index', function () {
    it('lists drafts and published insights', function () {
        $admin = User::factory()->admin()->create();
        Insight::factory()->create();
        Insight::factory()->published()->create();

        $response = $this->actingAs($admin)->get(route('admin.insights.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Insights/Index')
            ->has('insights', 2));
    });
});

describe('store', function () {
    it('creates a draft and opens it in the editor', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), insightPayload());

        $insight = Insight::sole();
        $response->assertRedirect(route('admin.insights.edit', $insight));
        expect($insight)
            ->title->toBe('Docker on Windows is slow')
            ->slug->toBe('docker-on-windows-is-slow')
            ->published_at->toBeNull();
    });

    it('sets the publish date when created as published', function () {
        $admin = User::factory()->admin()->create();
        Carbon::setTestNow('2026-10-27 12:00:00');

        $this->actingAs($admin)->post(route('admin.insights.store'), insightPayload(['is_published' => true]));

        expect(Insight::sole()->published_at->toDateTimeString())->toBe('2026-10-27 12:00:00');
    });

    it('generates the slug from the title when none is given', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.insights.store'), insightPayload([
            'title' => 'Why CI Comes Before CD',
            'slug' => '',
        ]));

        expect(Insight::sole()->slug)->toBe('why-ci-comes-before-cd');
    });

    it('requires a title, excerpt, body and publish state', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), []);

        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
            'excerpt' => 'The excerpt field is required.',
            'body' => 'The body field is required.',
            'is_published' => 'The is published field is required.',
        ]);
    });

    it('rejects a slug that is already taken', function () {
        $admin = User::factory()->admin()->create();
        Insight::factory()->create(['slug' => 'docker-on-windows-is-slow']);

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), insightPayload());

        $response->assertSessionHasErrors(['slug' => 'The slug has already been taken.']);
        expect(Insight::count())->toBe(1);
    });

    it('rejects a slug that is not URL friendly', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), insightPayload(['slug' => 'Not A Slug!']));

        $response->assertSessionHasErrors([
            'slug' => 'The slug may only contain lowercase letters, numbers, and single dashes.',
        ]);
    });
});

describe('update', function () {
    it('saves changes and keeps the insight its own slug', function () {
        $admin = User::factory()->admin()->create();
        $insight = Insight::factory()->create(['slug' => 'docker-on-windows-is-slow']);

        $response = $this->actingAs($admin)->put(route('admin.insights.update', $insight), insightPayload([
            'title' => 'Docker on Windows is really slow',
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.insights.edit', $insight));
        expect($insight->refresh()->title)->toBe('Docker on Windows is really slow');
    });

    it('keeps the original publish date when a published insight is saved again', function () {
        $admin = User::factory()->admin()->create();
        $insight = Insight::factory()->create(['published_at' => '2026-01-15 09:00:00']);

        $this->actingAs($admin)->put(route('admin.insights.update', $insight), insightPayload(['is_published' => true]));

        expect($insight->refresh()->published_at->toDateTimeString())->toBe('2026-01-15 09:00:00');
    });

    it('turns a published insight back into a draft', function () {
        $admin = User::factory()->admin()->create();
        $insight = Insight::factory()->published()->create();

        $this->actingAs($admin)->put(route('admin.insights.update', $insight), insightPayload(['is_published' => false]));

        expect($insight->refresh()->published_at)->toBeNull();
    });
});

describe('destroy', function () {
    it('deletes the insight', function () {
        $admin = User::factory()->admin()->create();
        $insight = Insight::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.insights.destroy', $insight));

        $response->assertRedirect(route('admin.insights.index'));
        $this->assertModelMissing($insight);
    });
});

describe('preview', function () {
    it('renders Markdown with raw HTML escaped', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.insights.preview'), [
            'body' => "## Heading\n\n<script>alert('xss')</script>",
        ]);

        expect($response->json('html'))
            ->toContain('<h2>Heading</h2>')
            ->toContain('&lt;script&gt;')
            ->not->toContain('<script>');
    });
});
