<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AreaArticle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreaArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all();

        if ($areas->isEmpty()) {
            $this->command->warn('No areas found. Please seed areas first.');
            return;
        }

        foreach ($areas as $area) {
            $this->command->info("Creating articles for area: {$area->name}");

            for ($i = 1; $i <= 3; $i++) {
                $title = $this->generateTitle($area->name, $i);

                // Skip if article already exists (match on the English title in the JSON column)
                if (AreaArticle::where('area_id', $area->id)->where('title->en', $title['en'])->exists()) {
                    $this->command->info("  - Article '{$title['en']}' already exists, skipping...");
                    continue;
                }

                AreaArticle::create([
                    'area_id' => $area->id,
                    'title' => $title,
                    'excerpt' => $this->generateExcerpt($area->name, $i),
                    'content' => $this->generateContent($area->name, $i),
                    'is_active' => $i <= 2, // First 2 articles active, 3rd inactive
                    'published_at' => $i <= 2 ? now()->subDays(rand(1, 30)) : null, // First 2 published, 3rd draft
                ]);

                $this->command->info("  ✓ Created: {$title['en']}");
            }
        }

        $this->command->info('Area articles seeded successfully!');
    }

    /**
     * Returns translations keyed by locale: ['en' => '...', 'sr' => '...', 'rsn' => '...'].
     * sr is Serbian (Latin), rsn is Rusyn (Cyrillic), matching the janikoke54 app.
     * spatie/laravel-translatable stores the array as JSON on the column.
     */
    private function generateTitle(string $areaName, int $index): array
    {
        $titles = [
            'en' => [
                1 => "Discover {areaName}",
                2 => "Things to Do in {areaName}",
                3 => "History of {areaName}",
            ],
            'sr' => [
                1 => "Otkrijte {areaName}",
                2 => "Šta raditi u {areaName}",
                3 => "Istorija {areaName}",
            ],
            'rsn' => [
                1 => "Виглєдуй {areaName}",
                2 => "Цо робиц у {areaName}",
                3 => "История {areaName}",
            ],
        ];

        return $this->localize($titles, $areaName, $index, [
            'en' => "Article about {areaName}",
            'sr' => "Članak o {areaName}",
            'rsn' => "Артикул о {areaName}",
        ]);
    }

    private function generateExcerpt(string $areaName, int $index): array
    {
        $excerpts = [
            'en' => [
                1 => "A short intro to {areaName}.",
                2 => "Things to do in {areaName}.",
                3 => "A bit of history about {areaName}.",
            ],
            'sr' => [
                1 => "Kratak uvod o {areaName}.",
                2 => "Šta raditi u {areaName}.",
                3 => "Malo istorije o {areaName}.",
            ],
            'rsn' => [
                1 => "Кратки увод о {areaName}.",
                2 => "Цо робиц у {areaName}.",
                3 => "Дакус историї о {areaName}.",
            ],
        ];

        return $this->localize($excerpts, $areaName, $index, [
            'en' => "This is the article about the location you just entered.",
            'sr' => "Ovo je članak o lokaciji koju ste upravo izabrali.",
            'rsn' => "То е артикул о локациї хтору сце лєм цо вибрали.",
        ]);
    }

    private function generateContent(string $areaName, int $index): array
    {
        $contents = [
            'en' => [
                1 => "<p>Welcome to {areaName}. This is the article about the location you just entered.</p>",
                2 => "<p>Discover the activities and attractions in {areaName}.</p>",
                3 => "<p>Learn a bit about the history and culture of {areaName}.</p>",
            ],
            'sr' => [
                1 => "<p>Dobrodošli u {areaName}. Ovo je članak o lokaciji koju ste upravo izabrali.</p>",
                2 => "<p>Otkrijte aktivnosti i znamenitosti u {areaName}.</p>",
                3 => "<p>Saznajte nešto o istoriji i kulturi {areaName}.</p>",
            ],
            'rsn' => [
                1 => "<p>Витайце до {areaName}. То е артикул о локациї хтору сце лєм цо вибрали.</p>",
                2 => "<p>Виглєдуй активносци и знаменитосци у {areaName}.</p>",
                3 => "<p>Дознай дашто о историї и култури {areaName}.</p>",
            ],
        ];

        return $this->localize($contents, $areaName, $index, [
            'en' => "<p>This is the article about the location you just entered.</p>",
            'sr' => "<p>Ovo je članak o lokaciji koju ste upravo izabrali.</p>",
            'rsn' => "<p>То е артикул о локациї хтору сце лєм цо вибрали.</p>",
        ]);
    }

    /**
     * Builds the per-locale array, applying the locale's fallback text when an
     * index has no entry and substituting the area name.
     */
    private function localize(array $byLocale, string $areaName, int $index, array $fallbacks): array
    {
        $out = [];

        foreach ($fallbacks as $locale => $fallback) {
            $text = $byLocale[$locale][$index] ?? $fallback;
            $out[$locale] = str_replace('{areaName}', $areaName, $text);
        }

        return $out;
    }
}
