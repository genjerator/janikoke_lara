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
        if (Schema::hasTable('prizes') && !Schema::hasColumn('prizes', 'cost')) {
            Schema::table('prizes', function (Blueprint $table) {
                $table->integer('cost')->default(10)->after('amount')->comment('How many score points needed to redeem 1 amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};
