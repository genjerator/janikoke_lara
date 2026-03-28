<?php

namespace App\Filament\Resources\AreaPrizeResource\Pages;

use App\Filament\Resources\AreaPrizeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAreaPrice extends CreateRecord
{
    protected static string $resource = AreaPrizeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Prize created successfully';
    }
}

