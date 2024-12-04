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
        Schema::create('blood_request_donors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blood_request_id');
            $table->unsignedBigInteger('donor_id');
            $table->enum('status', [
                'pending', 'completed', 'cancelled'
            ])->default('pending');
            $table->text('medical_records')->nullable();
            $table->integer('blood_amount')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('file_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['blood_request_id', 'donor_id']);
            $table->index(['blood_request_id', 'status']);
            $table->index(['donor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_request_donors');
    }
};
