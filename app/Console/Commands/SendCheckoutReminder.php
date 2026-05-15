<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendCheckoutReminder extends Command
{
    // Nama command untuk dijalankan di terminal
    protected $signature = 'presence:remind-checkout';

    protected $description = 'Kirim WA Reminder untuk karyawan yang belum Check-Out setelah 8 jam';

    public function handle()
    {
        // Set waktu batas: 8 jam dari waktu check-in (Bisa lu sesuaikan)
        // $batasWaktu = Carbon::now()->subHours(8);
        $batasWaktu = Carbon::now()->subSeconds(10);

        // Cari karyawan yang masuk kriteria[cite: 2]
        $presences = Presence::with('employee')
            ->whereDate('date', Carbon::today())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->where('is_reminded', false) // Belum pernah diingatkan[cite: 2]
            ->where('check_in', '<=', $batasWaktu) // Sudah lewat 8 jam[cite: 2]
            ->get();

        if ($presences->isEmpty()) {
            $this->info('Tidak ada karyawan yang perlu diingatkan saat ini.');
            return;
        }

        foreach ($presences as $presence) {
            $employee = $presence->employee;
            
            // Skip kalau data karyawan atau nomor HP-nya kosong[cite: 2]
            if (!$employee || empty($employee->phone_number)) {
                continue; 
            }

            $nama = $employee->fullname;
            $phone = $employee->phone_number;
            $checkInTime = Carbon::parse($presence->check_in)->format('H:i');

            // Format Pesan WA
            $pesan = "Halo *{$nama}* 👋\n\nSistem Corevo mencatat kamu sudah bekerja lebih dari 8 jam sejak Check-In pukul {$checkInTime}.\n\nJangan lupa untuk melakukan *Check-Out* hari ini di aplikasi ya agar jam kerjamu terekam dengan baik! 🙏";

            try {
                // Tembak API Node.js lokal kita (Nanti di Fase 3 kita bikin API-nya jalan di port 3000)
                $response = Http::withHeaders([
                    'x-api-key' => 'adminwa'
                ])->post('http://localhost:3000/send', [ // <-- PASTIKAN NAMBAK KE LOCALHOST
                    'number' => $phone,
                    'message' => $pesan
                ]);

                if ($response->successful()) {
                    // Update database biar nggak dikirim berulang kali[cite: 2]
                    $presence->update(['is_reminded' => true]);
                    $this->info("✅ Berhasil mengirim reminder ke {$nama} ({$phone})");
                } else {
                    $this->error("❌ Gagal mengirim ke {$nama}. Bot WA merespon dengan error.");
                    Log::error("WA Reminder API Error: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("⚠️ Gagal koneksi ke server Bot WA: " . $e->getMessage());
                Log::error("WA Reminder Connection Error: " . $e->getMessage());
            }
        }
    }
}