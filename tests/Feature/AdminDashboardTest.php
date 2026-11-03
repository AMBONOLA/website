<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Models\Insight;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

it('keeps the dashboard controller admin-only when registered outside the admin route group', function () {
    $user = User::factory()->create();
    Route::middleware('web')->get('/unguarded/dashboard', DashboardController::class);

    $response = $this->actingAs($user)->get('/unguarded/dashboard');

    $response->assertForbidden();
});

describe('site health', function () {
    it('shows the commit the site is running', function () {
        config(['app.commit' => 'abc1234def']);

        $response = $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('commit', 'abc1234def')
        );
    });

    it('reports a working database connection with its response time', function () {
        $response = $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('database.connected', true)
            ->where('database.latency_ms', fn (int $latency_ms) => $latency_ms >= 0)
        );
    });

    it('reports a failed database check without breaking the page', function () {
        Exceptions::fake();
        $failure = new RuntimeException('connection refused');
        DB::connection()->beforeExecuting(function (string $query) use ($failure) {
            if ($query === 'select 1') {
                throw $failure;
            }
        });

        $response = $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('database', ['connected' => false, 'latency_ms' => null])
        );
        Exceptions::assertReported(fn (RuntimeException $exception) => $exception === $failure);
    });
});

describe('series tracker', function () {
    it('lists drafts and published parts in part order', function () {
        Insight::factory()->create(['title' => 'Building my website, part 10: Later', 'slug' => 'part-ten']);
        $published_part = Insight::factory()->published()->create(['title' => 'Building my website, part 1: The tools.', 'slug' => 'part-one']);
        Insight::factory()->create(['title' => 'Building my website, part 2: Docker', 'slug' => 'part-two']);

        $response = $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('series.0', [
                'id' => $published_part->id,
                'part' => 1,
                'title' => 'The tools.',
                'slug' => 'part-one',
                'is_published' => true,
            ])
            ->where('series.1.part', 2)
            ->where('series.1.is_published', false)
            ->where('series.2.part', 10)
        );
    });

    it('leaves out insights that are not numbered parts of the series', function (string $title) {
        Insight::factory()->create(['title' => $title]);

        $response = $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'));

        $response->assertInertia(fn (Assert $page) => $page->has('series', 0));
    })->with([
        'unrelated title' => 'Docker on Windows is slow',
        'prefix without a number' => 'Building my website, part two',
    ]);
});
