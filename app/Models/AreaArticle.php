<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaArticle extends Model
{
    use HasFactory, HasUuids;

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
