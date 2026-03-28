<?php

namespace App\Filament\Resources\AreaPrizeResource\Pages;

use App\Filament\Resources\AreaPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAreaPrice extends ViewRecord
{
    protected static string $resource = AreaPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
}
