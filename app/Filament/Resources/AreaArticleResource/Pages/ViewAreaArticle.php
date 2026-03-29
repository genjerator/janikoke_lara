<?php

namespace App\Filament\Resources\AreaArticleResource\Pages;

use App\Filament\Resources\AreaArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAreaArticle extends ViewRecord
{
    protected static string $resource = AreaArticleResource::class;

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
