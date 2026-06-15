<?php

namespace App\Filament\Resources\AreaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';

    protected static ?string $title = 'Area Articles';

    /**
     * Locales the articles can be translated into. Matches the janikoke54
     * mobile app and the SetLocale middleware's supported locales.
     */
    private const LOCALES = [
        'en' => 'English',
        'sr' => 'Serbian',
        'rsn' => 'Rusyn',
    ];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Translations')
                    ->columnSpanFull()
                    ->tabs(collect(self::LOCALES)->map(
                        fn (string $label, string $locale) => Forms\Components\Tabs\Tab::make($label)
                            ->schema([
                                Forms\Components\TextInput::make("title.{$locale}")
                                    ->label('Article Title')
                                    ->required($locale === 'en')
                                    ->maxLength(255)
                                    ->placeholder('Enter article title'),

                                Forms\Components\Textarea::make("excerpt.{$locale}")
                                    ->label('Excerpt')
                                    ->rows(3)
                                    ->nullable()
                                    ->placeholder('Brief summary of the article (optional)'),

                                Forms\Components\RichEditor::make("content.{$locale}")
                                    ->label('Article Content')
                                    ->required($locale === 'en')
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
                            ])
                    )->values()->all()),

                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->nullable()
                    ->placeholder('Leave empty to publish immediately'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active articles will be visible'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    // title/excerpt/content are translatable JSON columns; query the
                    // default-locale path since Postgres can't sort/LIKE a json value.
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderByRaw("title->>'en' {$direction}"))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereRaw("title->>'en' ILIKE ?", ["%{$search}%"]))
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
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(fn (array $data): array => $this->stripEmptyTranslations($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(fn (array $data, $record): array => array_merge($data, [
                        'title' => $record->getTranslations('title'),
                        'excerpt' => $record->getTranslations('excerpt'),
                        'content' => $record->getTranslations('content'),
                    ]))
                    ->mutateFormDataUsing(fn (array $data): array => $this->stripEmptyTranslations($data)),
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

    /**
     * Remove blank locale values so untranslated languages are not stored as
     * empty strings — this keeps spatie's fallback to the default locale working.
     */
    private function stripEmptyTranslations(array $data): array
    {
        foreach (['title', 'excerpt', 'content'] as $field) {
            if (is_array($data[$field] ?? null)) {
                $data[$field] = array_filter(
                    $data[$field],
                    fn ($value) => filled($value)
                );
            }
        }

        return $data;
    }
}
