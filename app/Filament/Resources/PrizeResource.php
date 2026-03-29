<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrizeResource\Pages;
use App\Models\Prize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrizeResource extends Resource
{
    protected static ?string $model = Prize::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationLabel = 'Prizes';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Prize Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Prize Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter prize name')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->placeholder('0'),

                        Forms\Components\TextInput::make('cost')
                            ->label('Score Cost')
                            ->numeric()
                            ->default(10)
                            ->minValue(0)
                            ->required()
                            ->helperText('How many score points needed to redeem this prize')
                            ->placeholder('10'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                0 => 'Inactive',
                                1 => 'Active',
                                2 => 'Pending',
                                3 => 'Awarded',
                            ])
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->nullable()
                            ->placeholder('Brief description of the prize')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Content')
                            ->nullable()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->placeholder('Detailed content about the prize'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Prize Name')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Score Cost')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->suffix(' pts'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        0 => 'Inactive',
                        1 => 'Active',
                        2 => 'Pending',
                        3 => 'Awarded',
                        default => 'Unknown'
                    })
                    ->color(fn ($state) => match($state) {
                        0 => 'danger',
                        1 => 'success',
                        2 => 'warning',
                        3 => 'primary',
                        default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active',
                        2 => 'Pending',
                        3 => 'Awarded',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_active')
                        ->label('Mark as Active')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 1]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_awarded')
                        ->label('Mark as Awarded')
                        ->icon('heroicon-o-gift')
                        ->color('primary')
                        ->action(fn ($records) => $records->each->update(['status' => 3]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No prizes yet')
            ->emptyStateDescription('Create your first prize to get started')
            ->emptyStateIcon('heroicon-o-gift');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrizes::route('/'),
            'create' => Pages\CreatePrize::route('/create'),
            'view' => Pages\ViewPrize::route('/{record}'),
            'edit' => Pages\EditPrize::route('/{record}/edit'),
        ];
    }
}
