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
        if (!Schema::hasTable('area_articles')) {
            return;
        }

        // Check if id column is already UUID
        $columnType = \DB::getSchemaBuilder()->getColumnType('area_articles', 'id');
        if ($columnType === 'uuid') {
            return; // Already converted
        }

        // Delete all existing records (can be re-seeded)
        \DB::table('area_articles')->truncate();

        Schema::table('area_articles', function (Blueprint $table) {
            // Drop the old auto-increment ID
            $table->dropColumn('id');
        });

        Schema::table('area_articles', function (Blueprint $table) {
            // Add UUID as primary key
            $table->uuid('id')->primary()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_articles', function (Blueprint $table) {
            // Drop UUID primary key
            $table->dropPrimary('area_articles_pkey');
            $table->dropColumn('id');
        });

        Schema::table('area_articles', function (Blueprint $table) {
            // Restore auto-increment ID
            $table->id()->first();
        });
    }
};
