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
        $json = json_decode($data['json_polygon'], true);
        if ($json[0] !== $json[count($json) - 1]) {
            array_push($json,$json[0]);
        }
        $points = collect($json)->map(fn($coordinates) => new Point($coordinates['lat'], $coordinates['lng']));

        $lineString = new LineString($points);
        $polygon = new Polygon([$lineString]);
        $data['area'] = $polygon;
        unset($data['json_polygon']);

        return $data;
    }
}
