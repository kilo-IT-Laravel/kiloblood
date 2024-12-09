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
            $table->boolean('is_confirmed')->default(false);
            $table->unsignedBigInteger('blood_request_id');
            $table->unsignedBigInteger('requester_id');
            $table->enum('status', ['accepted', 'rejected'])->nullable();
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->index(['requester_id', 'status' , 'blood_request_id']);
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
