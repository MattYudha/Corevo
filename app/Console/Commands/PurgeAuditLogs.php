<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class PurgeAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     * Usage:  php artisan audit:purge --days=90
     */
    protected $signature = 'audit:purge
                            {--days=90 : Hapus log yang lebih lama dari N hari}
                            {--force  : Jalankan tanpa konfirmasi}';

    protected $description = 'Hapus audit log lama berdasarkan jumlah hari retensi';

    public function handle(): int
    {
        $days  = (int) $this->option('days');
        $before = now()->subDays($days)->toDateString();

        $count = AuditLog::whereDate('created_at', '<=', $before)->count();

        if ($count === 0) {
            $this->info("Tidak ada log yang lebih lama dari {$days} hari. Tidak ada yang dihapus.");
            return self::SUCCESS;
        }

        $this->warn("Ditemukan {$count} log sebelum {$before} yang akan dihapus.");

        if (!$this->option('force') && !$this->confirm("Lanjutkan?")) {
            $this->info('Dibatalkan.');
            return self::SUCCESS;
        }

        $deleted = AuditLog::whereDate('created_at', '<=', $before)->delete();

        $this->info("✅ {$deleted} audit log berhasil dihapus (sebelum {$before}).");

        return self::SUCCESS;
    }
}
