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
        Schema::create('blood_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_id');
            $table->unsignedBigInteger('blood_request_id');
            $table->unsignedBigInteger('blood_request_donor_id');
            $table->string('blood_type');
            $table->integer('quantity');
            $table->timestamp('donation_date');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->foreign('blood_request_donor_id')->references('id')->on('blood_request_donors')->onDelete('cascade');
            $table->index(['donor_id', 'donation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donations');
    }
};
