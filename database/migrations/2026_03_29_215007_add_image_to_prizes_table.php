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
        if (Schema::hasTable('prizes') && !Schema::hasColumn('prizes', 'image')) {
            Schema::table('prizes', function (Blueprint $table) {
                $table->string('image')->nullable()->after('content')->comment('Path to prize image file');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('prizes') && Schema::hasColumn('prizes', 'image')) {
            Schema::table('prizes', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
