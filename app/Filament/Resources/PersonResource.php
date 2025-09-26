<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Filament\Resources\PersonResource\RelationManagers\InfoRelationManager;
use App\Models\PeopleInfo;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('day_of_birth')
                    ->required(),
                DatePicker::make('day_of_die'),
                Select::make('area_id')
                    ->label('Area')
                    ->relationship(
                        name: 'area',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('type', 2)
                    )
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255)
                    ->columnSpanFull(),
//                Forms\Components\TextInput::make('info.description')
//                    ->label('Description')
//                    ->maxLength(255)
//                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('day_of_birth'),
                Tables\Columns\TextColumn::make('day_of_die'),
                Tables\Columns\TextColumn::make('area.name'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }

//    public static function getRelations(): array
//    {
//        return [
//            PeopleInfo::class,
//        ];
//    }
}
