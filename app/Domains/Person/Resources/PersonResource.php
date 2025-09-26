<?php

namespace App\Domains\Person\Resources;


use App\Models\Person;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Person $resource
 */
class PersonResource extends JsonResource
{
    public static $wrap = null;

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
            'id' => $person->id,
            'first_name' => $person->first_name,
            'last_name' => $person->last_name,
            'date_of_birth' => $person->day_of_birth->format('d.m.Y'),
            'date_of_die' => $person->day_of_die->format('d.m.Y'),
            'name' => $person->name(),
            'description' => $person->description,

        ];
    }
}
