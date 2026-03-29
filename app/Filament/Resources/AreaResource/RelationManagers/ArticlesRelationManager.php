<?php

namespace App\Filament\Resources\AreaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';

    protected static ?string $title = 'Area Articles';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Excerpt')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Draft')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('M d, Y') : 'Draft'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All articles')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Article')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No articles yet')
            ->emptyStateDescription('Add your first article for this area')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
