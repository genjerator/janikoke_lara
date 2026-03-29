<?php

namespace App\Filament\Resources\AreaArticleResource\Pages;

use App\Filament\Resources\AreaArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAreaArticle extends EditRecord
{
    protected static string $resource = AreaArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
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
        return 'Article updated successfully';
    }
}
