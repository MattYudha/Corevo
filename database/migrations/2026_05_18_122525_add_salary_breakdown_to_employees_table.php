<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('basic_salary', 15, 2)->default(0)->after('salary');
            $table->decimal('meal_allowance', 15, 2)->default(0)->after('basic_salary');
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('meal_allowance');
            $table->decimal('position_allowance', 15, 2)->default(0)->after('transport_allowance');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'meal_allowance', 'transport_allowance', 'position_allowance']);
        });
    }
};
