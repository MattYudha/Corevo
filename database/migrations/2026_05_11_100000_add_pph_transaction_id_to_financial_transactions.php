<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Link ke transaksi PPh yang dibuat otomatis oleh sistem
            $table->unsignedBigInteger('pph_transaction_id')
                  ->nullable()
                  ->after('tax_amount')
                  ->comment('ID transaksi kredit PPh yang dibuat otomatis oleh sistem');
        });

        // Tambahkan foreign key setelah kolom dibuat
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->foreign('pph_transaction_id')
                  ->references('id')
                  ->on('financial_transactions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropForeign(['pph_transaction_id']);
            $table->dropColumn('pph_transaction_id');
        });
    }
};
