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
        if (!Schema::hasTable('prize_round')) {
            Schema::create('prize_round', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('prize_id'); // No FK constraint per CLAUDE.md
                $table->integer('round_id'); // No FK constraint per CLAUDE.md
                $table->boolean('is_active')->default(true);
                $table->integer('custom_cost')->nullable()->comment('Override prize cost for this round (optional)');
                $table->timestamps();

                // Indexes
                $table->index('prize_id');
                $table->index('round_id');
                $table->unique(['prize_id', 'round_id']); // One prize per round
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_round');
    }
};
