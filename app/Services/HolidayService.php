<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HolidayService
{
    /**
     * Fetch the full holiday data (complete JSON).
     */
    public static function getHolidays($year, $month)
    {
        $cacheKey = "holidays_{$year}_{$month}";
        
        // Cache the data for 30 days to optimize API requests.
        return Cache::remember($cacheKey, 86400 * 30, function () use ($year, $month) {
            try {
                $response = Http::timeout(5)->get("https://libur.deno.dev/api", [
                    'year' => $year,
                    'month' => $month
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
                return [];
            } catch (\Exception $e) {
                \Log::error("Failed to fetch holiday API: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Fetch only the array of dates (Example: ['2026-05-01', '2026-05-14']).
     */
    public static function getHolidayDates($year, $month)
    {
        $holidays = self::getHolidays($year, $month);
        
        // Retrieve all holiday dates (including national holidays and collective leave).
        return array_map(function($holiday) {
            return $holiday['date'];
        }, $holidays);
    }
}