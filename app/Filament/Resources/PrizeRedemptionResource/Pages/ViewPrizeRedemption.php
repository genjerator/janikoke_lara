<?php

namespace App\Filament\Resources\PrizeRedemptionResource\Pages;

use App\Filament\Resources\PrizeRedemptionResource;
use App\Models\PrizeRedemption;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrizeRedemption extends ViewRecord
{
    protected static string $resource = PrizeRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        /** @var PrizeRedemption $record */
        $record = $this->getRecord();

        $actions = [];

        if (in_array($record->status, ['pending', 'approved'])) {
            $actions[] = Actions\Action::make('complete')
                ->label('Mark Complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $record->complete());

            $actions[] = Actions\Action::make('cancel')
                ->label('Cancel Redemption')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $record->cancel());
        }

        return $actions;
    }
}
