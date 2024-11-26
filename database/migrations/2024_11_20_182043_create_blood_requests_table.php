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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->unique();
            $table->string('blood_type');
            $table->enum('role', ['user', 'doctor'])->default('user');
            $table->timestamp('phonenumber_verified_at')->nullable()->default(null);
            $table->boolean('available_for_donation')->default(true);
            $table->string('image')->nullable();
            $table->string('location');
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['phone_number' ,'location' , 'created_at']);

        });

        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->string('blood_type');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('message')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
            
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['requester_id', 'status']);
        });

        Schema::create('blood_request_donors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blood_request_id');
            $table->unsignedBigInteger('donor_id');
            $table->enum('status', ['pending', 'accepted', 'confirmed', 'completed', 'rejected'])
                  ->default('pending');
            $table->text('medical_records')->nullable();
            $table->integer('blood_amount')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['blood_request_id', 'donor_id']);
            $table->index(['blood_request_id', 'status']);
            $table->index(['donor_id', 'status']);
        });

        Schema::create('hidden_blood_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('blood_request_id');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->unique(['user_id', 'blood_request_id']);
        });

        Schema::create('blood_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_id');
            $table->unsignedBigInteger('blood_request_id');
            $table->unsignedBigInteger('blood_request_donor_id');
            $table->string('blood_type');
            $table->integer('quantity');
            $table->timestamp('donation_date');
            $table->timestamps();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
            $table->foreign('blood_request_donor_id')->references('id')->on('blood_request_donors')->onDelete('cascade');
            $table->index(['donor_id', 'donation_date']);
        });

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

        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->text('message');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id' , 'type' ]);
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); 
            $table->index(['is_active', 'title' , 'order']);
        });

        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('language' , ['en' , 'kh' , 'ch'])->default('en');
            $table->timestamps();
            $table->index(['language' , 'title']);

        });

        Schema::create('social_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform')->nullable(); // idk about this gonna be invite other from social media or ? and if so what platforms ?
            $table->timestamps();
            $table->index(['user_id', 'platform']);
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
