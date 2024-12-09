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
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('language', ['en', 'kh', 'ch'])->default('en');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['language', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
