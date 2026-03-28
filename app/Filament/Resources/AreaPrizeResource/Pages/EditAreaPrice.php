<?php

namespace App\Filament\Resources\AreaPrizeResource\Pages;

use App\Filament\Resources\AreaPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAreaPrice extends EditRecord
{
    protected static string $resource = AreaPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Prize updated successfully';
    }
}

