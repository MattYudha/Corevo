<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement(
            "ALTER TABLE letters MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'printed') DEFAULT 'draft'",
        );
    }

    public function down()
    {
        DB::statement(
            "ALTER TABLE letters MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'printed') DEFAULT 'draft'",
        );
    }
};
