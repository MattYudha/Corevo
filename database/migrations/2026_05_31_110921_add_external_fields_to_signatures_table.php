<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            // Karena orang luar ga punya user_id, kita ubah user_id biar bisa null (opsional)
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Kolom buat nampung data orang luar
            $table->string('external_name')->nullable()->after('user_id');
            $table->string('external_email')->nullable()->after('external_name');
            $table->string('external_title')->nullable()->after('external_email');

            // Token unik buat akses link (sekali pakai)
            $table->string('token', 64)->unique()->nullable()->after('external_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            // Balikin lagi user_id jadi wajib (kalo di-rollback)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // Hapus kolom eksternal
            $table->dropColumn(['external_name', 'external_email', 'external_title', 'token']);
        });
    }
};
