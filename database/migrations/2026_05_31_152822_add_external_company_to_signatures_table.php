<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->string('external_company')->nullable()->after('external_title');
        });
    }

    public function down()
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn('external_company');
        });
    }
};
