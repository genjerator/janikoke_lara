<?php

namespace App\Filament\Resources\AreaPriceResource\Pages;

use App\Filament\Resources\AreaPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAreaPrice extends EditRecord
{
    protected static string $resource = AreaPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

