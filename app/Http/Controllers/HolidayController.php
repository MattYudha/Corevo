<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->get();
        return view('holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'type' => 'required|in:national,collective',
        ]);

        Holiday::create([
            'date' => $request->date,
            'name' => $request->name,
            'is_national_holiday' => $request->type === 'national',
            'is_collective_leave' => $request->type === 'collective',
        ]);

        return redirect()
            ->route('holidays.index')
            ->with(
                'success',
                'Holiday successfully added. Employees are automatically restricted from clocking in on this date.',
            );
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Holiday successfully deleted.');
    }

    public function sync()
    {
        try {
            // fetch data directly from the deno.dev api
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept'     => 'application/json'
                ])
                ->timeout(15)
                ->get('https://libur.deno.dev/api');

            if (!$response->successful()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed to fetch data from the API server.',
                    ],
                    500,
                );
            }

            $holidays = $response->json();

            if (!is_array($holidays)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid API format.',
                    ],
                    500,
                );
            }

            $count = 0;

            foreach ($holidays as $item) {
                if (isset($item['date']) && isset($item['name'])) {
                    
                    // parse with boolean filter so the string "false" is read as an actual boolean false
                    $isNationalHoliday = filter_var($item['is_national_holiday'] ?? true, FILTER_VALIDATE_BOOLEAN);
                    
                    // if it is not a national holiday, it is considered collective leave
                    $isCollectiveLeave = !$isNationalHoliday;

                    Holiday::updateOrCreate(
                        ['date' => $item['date']], // search key based on date
                        [
                            'name' => $item['name'],
                            'is_national_holiday' => $isNationalHoliday,
                            'is_collective_leave' => $isCollectiveLeave,
                        ]
                    );
                    
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Success. $count holidays have been successfully synchronized to the database.",
            ]);
            
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'A system error occurred: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
