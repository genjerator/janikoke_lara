<?php

namespace App\Filament\Resources\AreaArticleResource\Pages;

use App\Filament\Resources\AreaArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAreaArticle extends CreateRecord
{
    protected static string $resource = AreaArticleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Article created successfully';
    }
}
