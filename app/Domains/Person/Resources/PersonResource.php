<?php

namespace App\Domains\Person\Resources;


use App\Models\Person;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Person $resource
 */
class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $person = $this->resource;
        /**
         * @var Person $person
         */
        return [
            'id'            => $this->resource->id,
            'first_name'          => $this->resource->first_name,
            'last_name'          => $this->resource->last_name,
            'date_of_birth'          => $person->day_of_birth,
            'date_of_die'          => $person->day_of_birth,

        ];
    }
}
