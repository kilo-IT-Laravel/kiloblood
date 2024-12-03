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
            $table->timestamp('trusted_at')->nullable()->default(null);
            $table->string('location');
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['phone_number' ,'location' , 'created_at']);

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('phone_number')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
