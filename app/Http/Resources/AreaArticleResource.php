<?php

namespace App\Http\Resources;

use App\Models\AreaArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property AreaArticle $resource
 */
class AreaArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Translatable fields (title/excerpt/content) are accessed via the model
     * accessors, which return the string for the request locale resolved by the
     * SetLocale middleware. `area_id` is the join key the mobile app uses to
     * match an article to an area returned by /round/{round}.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'area_id' => $this->resource->area_id,
            'title' => $this->resource->title,
            'excerpt' => $this->resource->excerpt,
            'content' => $this->resource->content,
        ];
    }
}
