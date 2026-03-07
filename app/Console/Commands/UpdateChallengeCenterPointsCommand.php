<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateChallengeCenterPointsCommand extends Command
{
    protected $signature = 'challenges:update-center-points
                            {--dry-run : Preview without saving changes}
                            {--chunk=100 : Number of records to process at a time}';

    protected $description = 'Update center_point of each challenge to the centroid of all its linked areas';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        // Only process challenges that have at least one linked area with a polygon
        $total = DB::table('challenges')
            ->join('challenge_area', 'challenges.id', '=', 'challenge_area.challenge_id')
            ->join('areas', 'challenge_area.area_id', '=', 'areas.id')
            ->whereNotNull('areas.area')
            ->distinct()
            ->count('challenges.id');

        if ($total === 0) {
            $this->warn('No challenges with linked area polygons found.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} challenge(s) with linked area polygons.");

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        $updated = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::table('challenges')
            ->join('challenge_area', 'challenges.id', '=', 'challenge_area.challenge_id')
            ->join('areas', 'challenge_area.area_id', '=', 'areas.id')
            ->whereNotNull('areas.area')
            ->distinct()
            ->select('challenges.id')
            ->orderBy('challenges.id')
            ->chunk($chunkSize, function ($challenges) use ($dryRun, &$updated, &$skipped, $bar) {
                foreach ($challenges as $challenge) {
                    // Collect all polygons for this challenge and compute their combined centroid
                    $result = DB::selectOne("
                        SELECT ST_AsText(
                            ST_Centroid(
                                ST_Collect(a.area)
                            )
                        ) AS center_point
                        FROM challenge_area ca
                        JOIN areas a ON ca.area_id = a.id
                        WHERE ca.challenge_id = ?
                          AND a.area IS NOT NULL
                    ", [$challenge->id]);

                    if (! $result || ! $result->center_point) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    if (! $dryRun) {
                        DB::table('challenges')
                            ->where('id', $challenge->id)
                            ->update([
                                'center_point' => DB::raw(
                                    "ST_GeomFromText('{$result->center_point}', 4326)"
                                ),
                                'updated_at'   => now(),
                            ]);
                    } else {
                        $this->newLine();
                        $this->line("  Challenge ID {$challenge->id} → {$result->center_point}");
                    }

                    $updated++;
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("Done. Updated: {$updated} | Skipped (no centroid): {$skipped}");

        return self::SUCCESS;
    }
}
