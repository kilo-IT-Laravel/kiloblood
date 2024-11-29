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
        Schema::create('donated_bloods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_id');
            $table->string('blood_type');
            $table->integer('quantity');
            $table->timestamps();
            $table->foreign('donation_id')->references('id')->on('blood_donations')->onDelete('cascade');
            $table->index(['donation_id', 'blood_type']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donated_bloods');
    }
};
