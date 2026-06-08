<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

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
}
