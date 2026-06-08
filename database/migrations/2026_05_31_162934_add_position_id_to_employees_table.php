<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Bikin kolomnya dulu
            $table->unsignedBigInteger('position_id')->nullable()->after('role_id');

            // Bikin foreign key dengan NAMA CUSTOM 'fk_employee_position'
            $table
                ->foreign('position_id', 'fk_employee_position')
                ->references('position_id')
                ->on('positions')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop berdasarkan nama custom tadi
            $table->dropForeign('fk_employee_position');
            $table->dropColumn('position_id');
        });
    }
};
