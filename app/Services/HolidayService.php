<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;

class HolidayService
{
    /**
     * 1. AMBIL FULL DATA LIBUR (Untuk nampilin List Libur di Halaman Presences / Kalender)
     * Format output disamakan dengan API lama biar Frontend/Blade lu nggak error.
     */
    public static function getHolidays($year, $month)
    {
        return Holiday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->date,
                    'name' => $holiday->name,
                    'is_national_holiday' => (bool) $holiday->is_national_holiday,
                    'is_collective_leave' => (bool) $holiday->is_collective_leave,
                ];
            })
            ->toArray();
    }

    /**
     * 2. AMBIL ARRAY TANGGALNYA SAJA (Untuk logic pengecekan in_array)
     */
    public static function getHolidayDates($year, $month)
    {
        return Holiday::whereYear('date', $year)->whereMonth('date', $month)->pluck('date')->toArray();
    }

    /**
     * 3. MENGHITUNG HARI KERJA EFEKTIF (KHUSUS UNTUK PAYROLL)
     * Menghitung total Hari Kerja (Senin - Jumat) dikurangi Libur Nasional/Cuti Bersama
     * yang jatuh pada hari kerja (Senin - Jumat).
     */
    public static function getEffectiveWorkingDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // A. Hitung total hari Senin-Jumat di rentang tanggal tersebut
        $totalWeekdays = 0;
        $currentDate = $start->copy();

        while ($currentDate->lte($end)) {
            if ($currentDate->isWeekday()) {
                $totalWeekdays++;
            }
            $currentDate->addDay();
        }

        // B. Hitung hari libur yang jatuh HANYA pada Senin - Jumat
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date');
        $holidayOnWeekdaysCount = 0;

        foreach ($holidays as $date) {
            if (Carbon::parse($date)->isWeekday()) {
                $holidayOnWeekdaysCount++;
            }
        }

        // C. Kembalikan Hari Kerja Efektif
        return $totalWeekdays - $holidayOnWeekdaysCount;
    }

    /**
     * 4. BANTUAN: Menghitung Berapa Banyak Hari Libur di Hari Kerja (Untuk Info Slip Gaji)
     */
    public static function countWeekdayHolidays($startDate, $endDate)
    {
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date');
        $count = 0;

        foreach ($holidays as $date) {
            if (Carbon::parse($date)->isWeekday()) {
                $count++;
            }
        }

        return $count;
    }
}
