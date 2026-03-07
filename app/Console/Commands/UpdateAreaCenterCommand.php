<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAreaCenterCommand extends Command
{
    protected $signature = 'areas:update-centroids
                            {--dry-run : Preview without saving changes}
                            {--chunk=100 : Number of records to process at a time}';

    protected $description = 'Update the point field of each area to the centroid of its polygon';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $total = DB::table('areas')->whereNotNull('area')->count();

        if ($total === 0) {
            $this->warn('No areas with a polygon found.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} area(s) with a polygon.");

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        $updated = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::table('areas')
            ->whereNotNull('area')
            ->orderBy('id')
            ->chunk($chunkSize, function ($areas) use ($dryRun, &$updated, &$skipped, $bar) {
                foreach ($areas as $area) {
                    // Use PostGIS ST_Centroid to compute the center of the polygon
                    $centroid = DB::selectOne(
                        'SELECT ST_AsText(ST_Centroid(area)) AS centroid FROM areas WHERE id = ?',
                        [$area->id]
                    );

                    if (! $centroid || ! $centroid->centroid) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    if (! $dryRun) {
                        DB::table('areas')
                            ->where('id', $area->id)
                            ->update([
                                // ST_GeomFromText preserves the original SRID of the polygon
                                'point'      => DB::raw(
                                    "ST_SetSRID(ST_GeomFromText('{$centroid->centroid}'), ST_SRID(area))"
                                ),
                                'updated_at' => now(),
                            ]);
                    }

                    $updated++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();

        $this->info("Done. Updated: {$updated} | Skipped (null centroid): {$skipped}");

        return self::SUCCESS;
    }
}
