<?php

namespace App\Filament\Resources\AreaPrizeResource\Pages;

use App\Filament\Resources\AreaPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAreaPrices extends ListRecords
{
    protected static string $resource = AreaPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Prize')
                ->icon('heroicon-o-plus'),
        ];
    }
}

