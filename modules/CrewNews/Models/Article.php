<?php

namespace Modules\CrewNews\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $table = 'crewnews_articles';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'summary',
        'body',
        'image_path',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Only articles that are published and whose publish date has arrived. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    /** The body is written in Markdown; raw HTML is stripped. */
    public function getBodyHtmlAttribute(): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $this->body);

        // Be forgiving: "-Item" (no space after the dash) becomes a proper "- Item" bullet.
        $body = preg_replace('/^-(?=[A-Za-z])/m', '- ', $body);

        $html = Str::markdown($body, [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
            // Pressing Enter once = a line break (Markdown normally joins those lines).
            'renderer'           => ['soft_break' => "<br>\n"],
        ]);

        return str_replace(
            ['<img ', '<table>', '<blockquote>'],
            ['<img class="img-fluid rounded my-3" ', '<table class="table">', '<blockquote class="blockquote border-start border-4 ps-3">'],
            $html
        );
    }

    public static function makeSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
