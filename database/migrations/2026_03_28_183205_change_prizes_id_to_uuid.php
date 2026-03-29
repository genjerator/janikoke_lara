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

        // Check if id column is already UUID
        $columnType = \DB::getSchemaBuilder()->getColumnType('prizes', 'id');
        if ($columnType === 'uuid') {
            return; // Already converted
        }

        // Delete all existing records (can be re-seeded)
        \DB::table('prizes')->truncate();

        Schema::table('prizes', function (Blueprint $table) {
            // Drop the old auto-increment ID
            $table->dropColumn('id');
        });

        Schema::table('prizes', function (Blueprint $table) {
            // Add UUID as primary key
            $table->uuid('id')->primary()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            // Drop UUID primary key
            $table->dropPrimary('prizes_pkey');
            $table->dropColumn('id');
        });

        Schema::table('prizes', function (Blueprint $table) {
            // Restore auto-increment ID
            $table->id()->first();
        });
    }
};
