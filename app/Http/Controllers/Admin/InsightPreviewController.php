<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsightPreviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string'],
        ]);

        return response()->json([
            'html' => Insight::renderMarkdown($validated['body'] ?? ''),
        ]);
    }
}
