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

                // Skip if article already exists
                if (AreaArticle::where('area_id', $area->id)->where('title', $title)->exists()) {
                    $this->command->info("  - Article '{$title}' already exists, skipping...");
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

                $this->command->info("  ✓ Created: {$title}");
            }
        }

        $this->command->info('Area articles seeded successfully!');
    }

    private function generateTitle(string $areaName, int $index): string
    {
        $titles = [
            1 => "Discover the Beauty of {areaName}",
            2 => "Top Things to Do in {areaName}",
            3 => "History and Culture of {areaName}",
        ];

        return str_replace('{areaName}', $areaName, $titles[$index]);
    }

    private function generateExcerpt(string $areaName, int $index): string
    {
        $excerpts = [
            1 => "Discover what makes {areaName} a special destination. From its unique features to local attractions, this guide covers everything you need to know.",
            2 => "Looking for activities in {areaName}? Explore our curated list of experiences that showcase the best this area has to offer.",
            3 => "Dive deep into the rich history and culture of {areaName}. Learn about the stories and traditions that shape this remarkable place.",
        ];

        return str_replace('{areaName}', $areaName, $excerpts[$index]);
    }

    private function generateContent(string $areaName, int $index): string
    {
        $contents = [
            1 => "<h2>Welcome to {areaName}</h2>
<p>{areaName} is a vibrant area known for its unique character and diverse attractions. Whether you're a first-time visitor or a long-time resident, there's always something new to discover.</p>

<h3>What Makes This Area Special</h3>
<p>The charm of {areaName} lies in its authentic atmosphere and welcoming community. Visitors are drawn to its distinctive features and the warm hospitality of locals who call this place home.</p>

<h3>Getting Around</h3>
<p>Navigating {areaName} is straightforward, with various transportation options available. The area is well-connected and easily accessible, making it convenient for both quick visits and extended stays.</p>

<h3>Local Tips</h3>
<ul>
<li>Visit during early morning or late afternoon for the best experience</li>
<li>Don't miss the local specialties unique to this area</li>
<li>Take time to explore the quieter spots away from main attractions</li>
<li>Chat with locals to discover hidden gems</li>
</ul>

<p>Whether you're here for leisure or exploration, {areaName} offers a memorable experience that captures the essence of the region.</p>",

            2 => "<h2>Activities and Attractions in {areaName}</h2>
<p>From outdoor adventures to cultural experiences, {areaName} provides a diverse range of activities for all interests and ages.</p>

<h3>Popular Activities</h3>
<p>The area features numerous opportunities for recreation and entertainment. Whether you prefer active pursuits or leisurely exploration, you'll find plenty to keep you engaged.</p>

<h3>Seasonal Highlights</h3>
<p>Each season brings its own charm to {areaName}:</p>

<p>{areaName} welcomes families with various activities suitable for children and adults alike. Safe, engaging, and educational experiences make this a great destination for all ages.</p>

<h3>Local Events</h3>
<p>Throughout the year, {areaName} hosts community events and gatherings that showcase local culture and bring people together. Check local listings for current happenings during your visit.</p>",

            3 => "<h2>The Story of {areaName}</h2>
<p>Understanding the background of {areaName} adds depth to any visit. This area has evolved over time while maintaining its distinctive character.</p>

<h3>Historical Context</h3>
<p>The development of {areaName} reflects broader patterns in the region's growth. From its early days to the present, this area has played a significant role in the local community.</p>

<h3>Cultural Significance</h3>
<p>Today, {areaName} represents a blend of tradition and modernity. The area preserves its heritage while embracing contemporary developments, creating a unique cultural landscape.</p>

<blockquote>
<p>'{areaName} is more than just a place on a map – it's a living community with its own rhythm and personality.'</p>
</blockquote>
",
        ];

        return str_replace('{areaName}', $areaName, $contents[$index]);
    }
}
