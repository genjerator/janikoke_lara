<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaArticleResource\Pages;
use App\Models\AreaArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AreaArticleResource extends Resource
{
    protected static ?string $model = AreaArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Areas';

    protected static ?string $navigationLabel = 'Area Articles';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Article Information')
                    ->schema([
                        Forms\Components\Select::make('area_id')
                            ->label('Area')
                            ->relationship('area', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select an area'),

                        Forms\Components\TextInput::make('title')
                            ->label('Article Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter article title')
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->nullable()
                            ->placeholder('Leave empty to publish immediately'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active articles will be visible'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->nullable()
                            ->placeholder('Brief summary of the article (optional)')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Article Content')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                            ])
                            ->placeholder('Write your article content here...'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Excerpt')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->default('Not published'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Area')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All articles')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('published')
                    ->query(fn ($query) => $query->whereNotNull('published_at'))
                    ->label('Published only'),
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
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No articles yet')
            ->emptyStateDescription('Create your first area article to get started')
            ->emptyStateIcon('heroicon-o-document-text');
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
            'index' => Pages\ListAreaArticles::route('/'),
            'create' => Pages\CreateAreaArticle::route('/create'),
            'view' => Pages\ViewAreaArticle::route('/{record}'),
            'edit' => Pages\EditAreaArticle::route('/{record}/edit'),
        ];
    }
}
