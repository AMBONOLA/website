<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsightRequest;
use App\Models\Insight;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InsightController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Insights/Index', [
            'insights' => Insight::query()
                ->latest('updated_at')
                ->get(['id', 'title', 'slug', 'published_at', 'updated_at']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Insights/Edit', ['insight' => null]);
    }

    public function store(InsightRequest $request): RedirectResponse
    {
        $insight = $this->save(new Insight, $request);

        return redirect()->route('admin.insights.edit', $insight);
    }

    public function edit(Insight $insight): Response
    {
        return Inertia::render('Insights/Edit', ['insight' => $insight]);
    }

    public function update(InsightRequest $request, Insight $insight): RedirectResponse
    {
        $this->save($insight, $request);

        return redirect()->route('admin.insights.edit', $insight);
    }

    public function destroy(Insight $insight): RedirectResponse
    {
        $insight->delete();

        return redirect()->route('admin.insights.index');
    }

    private function save(Insight $insight, InsightRequest $request): Insight
    {
        $insight->fill($request->safe()->only(['title', 'slug', 'excerpt', 'body']));
        // Re-saving keeps the original publish date, so an edited insight doesn't jump to the top of the list.
        $insight->published_at = $request->boolean('is_published') ? ($insight->published_at ?? now()) : null;
        $insight->save();

        return $insight;
    }
}
