<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'min_wfo_full_time'],
            [
                'value' => '12',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'min_wfo_part_time'],
            [
                'value' => '4',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down(): void
    {
        // if migration is gettin rollback, delete this key
        DB::table('settings')
            ->whereIn('key', ['min_wfo_full_time', 'min_wfo_part_time'])
            ->delete();
    }
};