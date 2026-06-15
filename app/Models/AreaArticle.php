<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class AreaArticle extends Model
{
    use HasFactory, HasUuids, HasTranslations;

    /**
     * Attributes stored as JSON keyed by locale, e.g. {"en": "...", "fi": "..."}.
     * Accessing them returns the current app locale, falling back to the default.
     */
    public array $translatable = ['title', 'excerpt', 'content'];

    protected $fillable = [
        'area_id',
        'title',
        'excerpt',
        'content',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the area that owns the article.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
