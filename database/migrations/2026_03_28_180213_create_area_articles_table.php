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
            Schema::create('area_articles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->string('title');
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->boolean('is_active')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_articles');
    }
};
