<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DashboardController extends Controller implements HasMiddleware
{
    /**
     * An insight joins the series by its title, so a planned part is simply a draft named this way.
     */
    private const SERIES_TITLE_PREFIX = 'Building my website, part ';

    /**
     * Repeats the route group's checks so the controller stays admin-only even if a route is ever registered outside that group.
     *
     * @return array<int, string>
     */
    public static function middleware(): array
    {
        return ['auth', 'can:admin'];
    }

    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'commit' => config('app.commit'),
            'database' => $this->checkDatabase(),
            'series' => $this->seriesParts(),
        ]);
    }

    /**
     * @return array{connected: bool, latency_ms: int|null}
     */
    private function checkDatabase(): array
    {
        $started_at = hrtime(true);

        try {
            DB::select('select 1');
        } catch (Throwable $exception) {
            report($exception);

            return ['connected' => false, 'latency_ms' => null];
        }

        return [
            'connected' => true,
            'latency_ms' => (int) round((hrtime(true) - $started_at) / 1_000_000),
        ];
    }

    /**
     * @return array<int, array{id: int, part: int, title: string, slug: string, is_published: bool}>
     */
    private function seriesParts(): array
    {
        $title_pattern = '/^'.preg_quote(self::SERIES_TITLE_PREFIX, '/').'(\d+):?\s*(.*)$/';

        return Insight::query()
            ->where('title', 'like', self::SERIES_TITLE_PREFIX.'%')
            ->get(['id', 'title', 'slug', 'published_at'])
            ->map(function (Insight $insight) use ($title_pattern): ?array {
                if (preg_match($title_pattern, $insight->title, $matches) !== 1) {
                    return null;
                }

                return [
                    'id' => $insight->id,
                    'part' => (int) $matches[1],
                    'title' => $matches[2],
                    'slug' => $insight->slug,
                    'is_published' => $insight->published_at !== null,
                ];
            })
            ->filter()
            ->sortBy('part')
            ->values()
            ->all();
    }
}
