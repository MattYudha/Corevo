<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('letter_tags', function (Blueprint $table) {
            $table->string('dropdown_type')->nullable(); // 'manual' atau 'model'
            $table->text('dropdown_options')->nullable(); // kalau manual: "Pilihan 1, Pilihan 2, Pilihan 3"
            $table->string('dropdown_model')->nullable(); // kalau model: "OfficeLocation", "Employee"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_tags', function (Blueprint $table) {
            //
        });
    }
};
