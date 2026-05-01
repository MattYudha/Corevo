<?php

namespace App\Http\Controllers;

use App\Models\OvertimeSubmission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index()
    {
        $query = OvertimeSubmission::with('employee');
        
        if (!Auth::user()->isMasterAdmin()) {
            $query->where('employee_id', Auth::user()->employee_id);
        }
        
        $submissions = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
                             ->latest()
                             ->get();
                             
        return view('overtimes.index', compact('submissions'));
    }

    public function create()
    {
        return view('overtimes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required|string',
            'evidence' => 'nullable|file|max:5120',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        
        if ($end->lt($start)) {
            $end->addDay(); 
        }
        
        $path = $request->hasFile('evidence') ? $request->file('evidence')->store('overtimes', 'public') : null;

        OvertimeSubmission::create([
            'employee_id' => Auth::user()->employee_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $start->diffInMinutes($end),
            'description' => $request->description,
            'evidence_path' => $path,
        ]);

        return redirect()->route('overtimes.index')->with('success', 'Pengajuan lembur berhasil disimpan.');
    }

    public function show($id)
    {
        $overtime = OvertimeSubmission::with('employee')->findOrFail($id);
        return view('overtimes.show', compact('overtime'));
    }

    public function edit($id)
    {
        $overtime = OvertimeSubmission::findOrFail($id);
        if ($overtime->status !== 'pending') abort(403, 'Pengajuan yang sudah diproses tidak dapat diubah.');
        return view('overtimes.edit', compact('overtime'));
    }

    public function update(Request $request, $id)
    {
        $overtime = OvertimeSubmission::findOrFail($id);
        if ($overtime->status !== 'pending') abort(403, 'Pengajuan yang sudah diproses tidak dapat diubah.');

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required|string',
            'evidence' => 'nullable|file|max:5120',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        
        if ($end->lt($start)) {
            $end->addDay(); 
        }
        
        $path = $overtime->evidence_path;
        if ($request->hasFile('evidence')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('evidence')->store('overtimes', 'public');
        }

        $overtime->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $start->diffInMinutes($end),
            'description' => $request->description,
            'evidence_path' => $path,
        ]);

        return redirect()->route('overtimes.index')->with('success', 'Pengajuan lembur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $overtime = OvertimeSubmission::findOrFail($id);
        $isAdmin = Auth::user()->isMasterAdmin();
        $isOwner = Auth::user()->employee_id == $overtime->employee_id;

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus ini.');
        }

        if ($overtime->status == 'approved' && !$isAdmin) {
            abort(403, 'Hanya Master Admin yang dapat menghapus pengajuan yang telah disetujui.');
        }
        
        if ($overtime->evidence_path) {
            Storage::disk('public')->delete($overtime->evidence_path);
        }
        
        $overtime->delete();

        return redirect()->route('overtimes.index')->with('success', 'Pengajuan lembur berhasil dihapus.');
    }

    // --- FUNGSI APPROVE & REJECT MASSAL ---
    public function approveBatch(Request $request)
    {
        OvertimeSubmission::whereIn('id', $request->ids)->update(['status' => 'approved', 'approved_by' => Auth::id()]);
        return response()->json(['success' => true]);
    }

    public function rejectBatch(Request $request)
    {
        OvertimeSubmission::whereIn('id', $request->ids)->update(['status' => 'rejected', 'approved_by' => Auth::id()]);
        return response()->json(['success' => true]);
    }

    // --- FUNGSI UPDATE SETTINGS UANG LEMBUR ---
    public function updateSettings(Request $request)
    {
        $request->validate(['overtime_rate_per_hour' => 'required|numeric|min:0']);
        Setting::updateOrCreate(
            ['key' => 'overtime_rate_per_hour'],
            ['value' => $request->overtime_rate_per_hour]
        );
        return redirect()->back()->with('success', 'Tarif lembur berhasil diperbarui.');
    }
}