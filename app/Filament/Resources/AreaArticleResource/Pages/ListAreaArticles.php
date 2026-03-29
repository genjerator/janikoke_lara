<?php

namespace App\Filament\Resources\AreaArticleResource\Pages;

use App\Filament\Resources\AreaArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAreaArticles extends ListRecords
{
    protected static string $resource = AreaArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Article')
                ->icon('heroicon-o-plus'),
        ];
    }
}
