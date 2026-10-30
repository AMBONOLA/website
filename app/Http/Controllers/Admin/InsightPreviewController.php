<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class InsightPreviewController extends Controller implements HasMiddleware
{
    /**
     * Repeats the route group's checks so the controller stays admin-only even if a route is ever registered outside that group.
     *
     * @return array<int, string>
     */
    public static function middleware(): array
    {
        return ['auth', 'can:admin'];
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:'.Insight::MAX_BODY_LENGTH],
        ]);

        return response()->json([
            'html' => Insight::sanitizeHtml($validated['body'] ?? ''),
        ]);
    }
}
