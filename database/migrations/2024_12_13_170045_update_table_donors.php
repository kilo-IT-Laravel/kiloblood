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
        Schema::table('blood_request_donors', function (Blueprint $table) {
            $table->integer('confirmed_quantity')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_request_donors', function (Blueprint $table) {
            $table->dropColumn('confirmed_quantity');
        });
    }
};
