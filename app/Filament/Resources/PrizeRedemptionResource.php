<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrizeRedemptionResource\Pages;
use App\Models\PrizeRedemption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrizeRedemptionResource extends Resource
{
    protected static ?string $model = PrizeRedemption::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Prize Redemptions';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Redemption Details')
                    ->schema([
                        Forms\Components\TextInput::make('prize_name')
                            ->label('Prize Name')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('redemption_code')
                            ->label('Redemption Code')
                            ->disabled(),

                        Forms\Components\TextInput::make('score_cost')
                            ->label('Score Cost')
                            ->disabled()
                            ->suffix(' pts'),

                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('redeemed_at')
                            ->label('Redeemed At')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('prize_name')
                    ->label('Prize')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('score_cost')
                    ->label('Score Cost')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->suffix(' pts'),

                Tables\Columns\TextColumn::make('redemption_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($state),
                    })
                    ->color(fn ($state) => match($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('redeemed_at')
                    ->label('Redeemed')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\Filter::make('redeemed_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('redeemed_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('redeemed_at', '<=', $date));
                    })
                    ->label('Date Range'),

                Tables\Filters\Filter::make('user_search')
                    ->form([
                        Forms\Components\TextInput::make('user_name')
                            ->label('User Name'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['user_name'],
                            fn ($q, $name) => $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$name}%"))
                        );
                    })
                    ->label('User Search'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\Action::make('complete')
                        ->label('Mark Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (PrizeRedemption $record) => in_array($record->status, ['pending', 'approved']))
                        ->action(fn (PrizeRedemption $record) => $record->complete()),

                    Tables\Actions\Action::make('cancel')
                        ->label('Cancel')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (PrizeRedemption $record) => in_array($record->status, ['pending', 'approved']))
                        ->action(fn (PrizeRedemption $record) => $record->cancel()),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_complete')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->complete())
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('bulk_cancel')
                        ->label('Cancel Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->cancel())
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No redemptions yet')
            ->emptyStateDescription('Redemptions will appear here when users redeem prizes')
            ->emptyStateIcon('heroicon-o-ticket');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrizeRedemptions::route('/'),
            'view' => Pages\ViewPrizeRedemption::route('/{record}'),
        ];
    }
}
