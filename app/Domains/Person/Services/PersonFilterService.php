<?php

namespace App\Domains\Person\Services;

use App\Models\Person;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PersonFilterService extends EloquentFilter
{
    public function buildFilters(): void
    {
        switch (true) {
            case array_key_exists('first_name', $this->getRequestData()):
                $this->setStringLikeFilter('first_name');
            case array_key_exists('last_name', $this->getRequestData()):
                $this->setStringLikeFilter('last_name');
            case array_key_exists('dd_start_date', $this->getRequestData()):
                $this->setDateFromToFilter('dd_start_date');
            case array_key_exists('dd_end_date', $this->getRequestData()):
                $this->setDateFromToFilter('dd_end_date');
        }
    }

    public function getPersonArea(){
        $coordinates = $this->getQueryBuilder()->with('area')->get()->mapWithKeys(function ($person) {
            return [$person->id=>$person->area->getPolygonCoordinatesForLeaflet()];
        });
        return $coordinates;
    }


}
