<?php

namespace App\Models;

use Database\Factories\InsightFactory;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'slug', 'excerpt', 'body'])]
class Insight extends Model
{
    /** @use HasFactory<InsightFactory> */
    use HasFactory;

    /**
     * Caps the work the sanitizer does per request (~200 KB is far beyond any real post).
     */
    public const MAX_BODY_LENGTH = 200_000;

    private static ?HTMLPurifier $purifier = null;

    /**
     * Allowlist-based: anything not listed here (scripts, event handlers, iframes, unsafe URLs,
     * other CSS properties such as position or url()) is stripped, so the result is safe to output unescaped.
     * Keep in sync with what the admin editor (RichTextEditor.tsx) can produce.
     */
    public static function sanitizeHtml(string $html): string
    {
        return static::purifier()->purify($html);
    }

    private static function purifier(): HTMLPurifier
    {
        if (static::$purifier !== null) {
            return static::$purifier;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Allowed', implode(',', [
            'p[style]', 'br', 'h2[style]', 'h3[style]', 'h4[style]',
            'strong', 'em', 'u', 's', 'code', 'pre', 'blockquote', 'hr',
            'ul', 'ol', 'li', 'span[style]', 'a[href|target|rel]',
        ]));
        $config->set('CSS.AllowedProperties', ['font-family', 'font-size', 'color', 'background-color', 'text-align']);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('Attr.AllowedRel', ['noopener', 'noreferrer', 'nofollow']);

        return static::$purifier = new HTMLPurifier($config);
    }

    /**
     * Based on an average reading speed of ~200 words per minute.
     */
    public function readingMinutes(): int
    {
        $word_count = str_word_count(strip_tags(str_replace('<', ' <', $this->body)));

        return max(1, (int) ceil($word_count / 200));
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Sanitized on write, so every save path (admin form, seeders, tinker) stores safe HTML,
     * and again on read, so HTML written to the database directly is never served raw.
     */
    protected function body(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => static::sanitizeHtml($value),
            set: fn (string $value): string => static::sanitizeHtml($value),
        );
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
