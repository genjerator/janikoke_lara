<?php

namespace App\Filament\Resources\PersonInfoResource\Pages;

use App\Filament\Resources\PersonInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonInfos extends ListRecords
{
    protected static string $resource = PersonInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
