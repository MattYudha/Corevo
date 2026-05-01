<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // add overtime tracking columns to the existing presences table
        Schema::table('presences', function (Blueprint $table) {
            $table->boolean('is_overtime_requested')->default(false);
            $table->text('overtime_reason')->nullable();
            $table->enum('overtime_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamp('overtime_at')->nullable();
        });
    }

    public function down()
    {
        // clean up the columns if we reverse this migration
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn([
                'is_overtime_requested', 
                'overtime_reason', 
                'overtime_status', 
                'overtime_at'
            ]);
        });
    }
};