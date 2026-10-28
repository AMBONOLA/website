<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function index(): View
    {
        return view('insights.index', [
            'insights' => Insight::published()->latest('published_at')->simplePaginate(10),
        ]);
    }

    public function show(Insight $insight): View
    {
        abort_unless($insight->isPublished(), 404);

        return view('insights.show', ['insight' => $insight]);
    }
}
