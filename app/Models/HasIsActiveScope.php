<?php
namespace App\Models;

use App\Models\Scopes\IsActiveScope;

trait HasIsActiveScope
{
    /**
     * Apply the is_active global scope when explicitly called.
     */
    public function scopeOnlyActive($query)
    {
        return $query->withGlobalScope('is_active', new IsActiveScope);
    }
}
