<?php

namespace App\Filament\Resources\PersonInfoResource\Pages;

use App\Filament\Resources\PersonInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonInfo extends EditRecord
{
    protected static string $resource = PersonInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
