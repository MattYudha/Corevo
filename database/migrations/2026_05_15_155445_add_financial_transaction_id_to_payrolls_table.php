<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll', function (Blueprint $table) {
            $table->unsignedBigInteger('financial_transaction_id')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('payroll', function (Blueprint $table) {
            $table->dropColumn('financial_transaction_id');
        });
    }
};
