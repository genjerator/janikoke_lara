<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use App\Models\Area;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class CreateArea extends CreateRecord
{
    protected static string $resource = AreaResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Area::createPolygonFromData($data);
    }

}
