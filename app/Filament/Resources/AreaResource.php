<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Filament\Resources\AreaResource\RelationManagers;
use App\Forms\Components\LatLngJsonField;
use App\Models\Area;
use App\Models\Challenge;
use App\Models\Round;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter the name'),

                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(4)
                    ->placeholder('Enter a description'),

                LatLngJsonField::make('json_polygon')
                    ->label('Area Coordinates (Lat/Lng JSON)')
                    ->required()
                    ->helperText('Enter the area coordinates as a JSON array of objects with latitude and longitude.')
                // other fields
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name ss')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descriptionsss')
                    ->limit(50)
                    ->wrap(),
                ToggleColumn::make('is_active')
                    ->label('Active Status')
                    ->onColor('success')
                    ->offColor('secondary')
                    ->action(function (Area $record) {
                        $record->is_active = !$record->is_active;
                        $record->save();
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
