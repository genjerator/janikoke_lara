<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaPrize extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'name',
        'price',
        'description',
        'content',
        'is_active',
        'duration_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
    ];

    /**
     * Get the area that owns the price.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}

