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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_id');
            $table->enum('status', [
                'pending',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->string('blood_type');
            $table->string('name');
            $table->string('location');
            $table->integer('quantity')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->string('medical_records')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['donor_id', 'status' , 'created_at']);
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['donor_id' , 'status']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
