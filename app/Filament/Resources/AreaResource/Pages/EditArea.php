<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class EditArea extends EditRecord
{
    protected static string $resource = AreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $json = json_decode($data['json_polygon'],true);
// Map each point to a MatanYadaev\EloquentSpatial\Objects\Point object
        $points = collect($json)->map(fn ($coordinates) => new Point($coordinates['latitude'], $coordinates['longitude']));

// Create a LineString object with the mapped points
        $lineString = new LineString($points);

// Create a Polygon object using the LineString
        $polygon = new Polygon([$lineString]);

// Dump the Polygon object
        $data['area'] = $polygon;

        return $data;
    }
}
