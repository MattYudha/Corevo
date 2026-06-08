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
        Schema::create('letter_tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_name')->unique(); // contoh: 'nama', 'tanggal_masuk' (tanpa kurung siku)
            $table->string('input_type')->default('text'); // text, date, number, dll
            $table->longText('default_value')->nullable(); // Default value kalau ada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_tags');

        Schema::table('letter_tags', function (Blueprint $table) {
            // Kembalikan ke string/varchar jika di-rollback
            $table->string('default_value', 255)->nullable()->change();
        });
    }
};
