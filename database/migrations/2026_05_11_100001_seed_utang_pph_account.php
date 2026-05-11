<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Buat akun "Utang PPh" di CoA jika belum ada.
     * Akun ini digunakan sebagai target kredit dari transaksi PPh otomatis.
     */
    public function up(): void
    {
        $exists = DB::table('financial_accounts')
            ->where('code', '2100')
            ->exists();

        if (!$exists) {
            DB::table('financial_accounts')->insert([
                'code'        => '2100',
                'name'        => 'Utang PPh',
                'category'    => 'liability',
                'description' => 'Kewajiban PPh yang dipotong dan belum disetor ke negara (PPh 21, PPh 23, PPh 4 Ayat 2). Dibuat otomatis oleh sistem.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Hanya hapus jika tidak ada transaksi yang menggunakan akun ini
        $accountId = DB::table('financial_accounts')->where('code', '2100')->value('id');

        if ($accountId) {
            $hasTransactions = DB::table('financial_transactions')
                ->where('account_id', $accountId)
                ->whereNull('deleted_at')
                ->exists();

            if (!$hasTransactions) {
                DB::table('financial_accounts')->where('id', $accountId)->delete();
            }
        }
    }
};
