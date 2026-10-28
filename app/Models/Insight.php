<?php

namespace App\Models;

use Database\Factories\InsightFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'excerpt', 'body'])]
class Insight extends Model
{
    /** @use HasFactory<InsightFactory> */
    use HasFactory;

    /**
     * Raw HTML is escaped and unsafe links (e.g. javascript:) are dropped, so the result is safe to output unescaped.
     */
    public static function renderMarkdown(string $markdown): string
    {
        return Str::markdown($markdown, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    public function bodyHtml(): string
    {
        return static::renderMarkdown($this->body);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
