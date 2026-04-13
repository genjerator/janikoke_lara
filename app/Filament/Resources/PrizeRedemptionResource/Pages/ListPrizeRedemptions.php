<?php

namespace App\Filament\Resources\PrizeRedemptionResource\Pages;

use App\Filament\Resources\PrizeRedemptionResource;
use Filament\Resources\Pages\ListRecords;

class ListPrizeRedemptions extends ListRecords
{
    protected static string $resource = PrizeRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
