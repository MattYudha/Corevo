<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // create the settings table to store dynamic configs like wfo_start_time
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); 
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        // drop the table if we roll back
        Schema::dropIfExists('settings');
    }
};