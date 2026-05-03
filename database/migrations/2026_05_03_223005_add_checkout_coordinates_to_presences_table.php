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
        Schema::table('presences', function (Blueprint $table) {
            // Add specific coordinates for check-out
            $table->string('check_out_latitude')->nullable()->after('longitude');
            $table->string('check_out_longitude')->nullable()->after('check_out_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn(['check_out_latitude', 'check_out_longitude']);
        });
    }
};
