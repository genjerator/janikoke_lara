<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('prizes')) {
            return;
        }

        $driver = \DB::connection()->getDriverName();
        echo "DEBUG: Database driver is: $driver\n";

        // SQLite doesn't support dropping foreign keys, so skip this entire migration for SQLite
        if ($driver === 'sqlite') {
            echo "DEBUG: Skipping migration for SQLite\n";
            // Disable foreign keys for SQLite and drop columns without foreign key constraints
            \DB::statement('PRAGMA foreign_keys = OFF');

            Schema::table('prizes', function (Blueprint $table) {
                $columnsToCheck = ['user_id', 'challenge_area_id', 'round_id'];
                $existingColumns = array_filter($columnsToCheck, fn($col) => Schema::hasColumn('prizes', $col));
                if (!empty($existingColumns)) {
                    $table->dropColumn($existingColumns);
                }
            });

            \DB::statement('PRAGMA foreign_keys = ON');
            return;
        }

        echo "DEBUG: Executing migration for non-SQLite database\n";

        // For other databases, drop foreign keys first, then columns
        Schema::table('prizes', function (Blueprint $table) {
            $columnsToCheck = ['user_id', 'challenge_area_id', 'round_id'];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('prizes', $column)) {
                    try {
                        $table->dropForeign('prizes_' . $column . '_foreign');
                    } catch (\Exception $e) {
                        // Foreign key might not exist, continue
                    }
                }
            }

            // Drop columns that exist
            $existingColumns = array_filter($columnsToCheck, fn($col) => Schema::hasColumn('prizes', $col));
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = \DB::connection()->getDriverName();

        // SQLite doesn't support adding foreign keys the same way
        if ($driver === 'sqlite') {
            return;
        }

        Schema::table('prizes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('challenge_area_id')->nullable()->constrained('challenge_area')->onDelete('cascade');
            $table->foreignId('round_id')->nullable()->constrained('rounds')->onDelete('cascade');
        });
    }
};
