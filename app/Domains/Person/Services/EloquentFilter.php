<?php

namespace App\Domains\Person\Services;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class EloquentFilter
{
    private Builder $queryBuilder;

    public function __construct(private readonly array $requestData)
    {
        $this->queryBuilder = Person::query();
        $this->buildFilters();
    }

    public function setStringLikeFilter(string $filterName): void
    {

        if (!array_key_exists($filterName, $this->requestData)) {
            return;
        }

        $name = $this->requestData[$filterName] ?? [];
        $this->queryBuilder->where($filterName, 'like', '%' . $name . '%');
    }

    public function setDateFromToFilter(string $filterName): void
    {
        dump($this->requestData,$filterName);

        $date = $this->requestData[$filterName] ?? '';
        $parts = explode('_', $filterName);
        if (!array_key_exists($filterName, $this->requestData)) {
            return;
        }
        $formattedDate = Carbon::createFromFormat('d-m-Y', $date);
        $filterName = $parts[0]==='dd'?"day_of_die":'';
        if($parts[1]==='start') {
            $this->queryBuilder->whereDate($filterName, '>=', $formattedDate);
        }elseif($parts[1]=='end'){
            $this->queryBuilder->whereDate($filterName, '<=', $formattedDate);
        }
    }

    abstract public function buildFilters(): void;

    public function getResults(): Collection
    {
        dump($this->queryBuilder->toSql());
        return $this->queryBuilder->get();
    }

    public function getQueryBuilder(){
        return $this->queryBuilder;
    }

    public function getRequestData(){
        return $this->requestData;
    }
}
