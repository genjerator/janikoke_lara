<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('prize_redemptions')) {
            Schema::create('prize_redemptions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedBigInteger('user_id'); // No FK constraint — users.id is integer
                $table->uuid('prize_id');              // No FK constraint — prizes.id is UUID

                // Snapshot data at redemption time
                $table->string('prize_name');
                $table->integer('prize_amount');
                $table->integer('score_cost');

                // Redemption details
                $table->string('status')->default('approved'); // auto-approve for MVP
                $table->string('redemption_code')->unique();
                $table->text('notes')->nullable();

                // Timestamps
                $table->timestamp('redeemed_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();

                $table->timestamps();

                // Indexes
                $table->index('user_id');
                $table->index('prize_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prize_redemptions');
    }
};
