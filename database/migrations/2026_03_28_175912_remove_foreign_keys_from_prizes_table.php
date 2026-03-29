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

        Schema::table('prizes', function (Blueprint $table) {
            $columnsToCheck = ['user_id', 'challenge_area_id', 'round_id'];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('prizes', $column)) {
                    // Try to drop foreign key (may not exist, so wrap in try-catch)
                    try {
                        $table->dropForeign(['prizes_' . $column . '_foreign']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist or have different name, continue
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
        Schema::table('prizes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('challenge_area_id')->nullable()->constrained('challenge_area')->onDelete('cascade');
            $table->foreignId('round_id')->nullable()->constrained('rounds')->onDelete('cascade');
        });
    }
};
