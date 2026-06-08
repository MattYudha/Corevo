<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        // order by position_name
        $positions = Position::orderBy('position_name', 'asc')->get();
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255|unique:positions,position_name',
            'description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:100',
            'salary_grade' => 'nullable|string|max:100',
        ]);

        Position::create($request->only('position_name', 'description', 'title', 'level', 'salary_grade'));

        return redirect()->route('positions.index')->with('success', 'Position successfully added.');
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'position_name' =>
                'required|string|max:255|unique:positions,position_name,' . $position->position_id . ',position_id',
            'description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:100',
            'salary_grade' => 'nullable|string|max:100',
        ]);

        $position->update($request->only('position_name', 'description', 'title', 'level', 'salary_grade'));

        return redirect()->route('positions.index')->with('success', 'Position successfully updated.');
    }

    public function destroy(Position $position)
    {
        // check if this position is currently assigned to any employees
        if ($position->employeePositions()->exists()) {
            return redirect()
                ->route('positions.index')
                ->with('error', 'Failed to delete! This position is currently in use by employees.');
        }

        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Position successfully deleted.');
    }
}
