<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Translatable columns converted to JSON keyed by locale (spatie/laravel-translatable).
     */
    private array $columns = ['title', 'excerpt', 'content'];

    /**
     * Convert plain text columns to JSON, wrapping existing values under the
     * default locale so no data is lost: "Hello" => {"en": "Hello"}.
     */
    public function up(): void
    {
        if (!Schema::hasTable('area_articles')) {
            return;
        }

        $locale = config('app.fallback_locale', 'en');

        foreach ($this->columns as $column) {
            if (!Schema::hasColumn('area_articles', $column) || $this->isJson($column)) {
                continue; // Already converted or missing — safe to re-run.
            }

            // Postgres requires a USING clause to cast text -> json. NULLs stay NULL.
            DB::statement("
                ALTER TABLE area_articles
                ALTER COLUMN {$column} TYPE json
                USING CASE
                    WHEN {$column} IS NULL THEN NULL
                    ELSE json_build_object('{$locale}', {$column})
                END
            ");
        }
    }

    /**
     * Revert to plain text by extracting the default locale value.
     */
    public function down(): void
    {
        if (!Schema::hasTable('area_articles')) {
            return;
        }

        $locale = config('app.fallback_locale', 'en');

        foreach ($this->columns as $column) {
            if (!Schema::hasColumn('area_articles', $column) || !$this->isJson($column)) {
                continue;
            }

            DB::statement("
                ALTER TABLE area_articles
                ALTER COLUMN {$column} TYPE text
                USING ({$column}->>'{$locale}')
            ");
        }
    }

    private function isJson(string $column): bool
    {
        $row = DB::selectOne(
            'SELECT data_type FROM information_schema.columns WHERE table_name = ? AND column_name = ?',
            ['area_articles', $column]
        );

        return $row && in_array($row->data_type, ['json', 'jsonb'], true);
    }
};
