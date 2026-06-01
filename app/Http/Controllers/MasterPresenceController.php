<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\Presence;

class MasterPresenceController extends Controller
{
    public function index()
    {
        $settings = [
            'work_start_time' => Setting::getValue('work_start_time', '08:00'),
            'enable_late_wfo' => Setting::getValue('enable_late_wfo', '1'),
            'late_threshold_wfo' => Setting::getValue('late_threshold_wfo', '15'),

            'enable_late_wfh' => Setting::getValue('enable_late_wfh', '0'),
            'late_threshold_wfh' => Setting::getValue('late_threshold_wfh', '15'),

            'enable_late_wfa' => Setting::getValue('enable_late_wfa', '0'),
            'late_threshold_wfa' => Setting::getValue('late_threshold_wfa', '15'),

            'overtime_rate_per_hour' => Setting::getValue('overtime_rate_per_hour', '50000'),
        ];

        return view('master-presences.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'min_wfo_full_time' => 'required|integer|min:0',
            'min_wfo_part_time' => 'required|integer|min:0',
            'work_start_time' => 'required|date_format:H:i',
            'enable_late_wfo' => 'required|in:0,1',
            'late_threshold_wfo' => 'nullable|integer|min:0',
            'enable_late_wfh' => 'required|in:0,1',
            'late_threshold_wfh' => 'nullable|integer|min:0',
            'enable_late_wfa' => 'required|in:0,1',
            'late_threshold_wfa' => 'nullable|integer|min:0',
            'overtime_rate_per_hour' => 'nullable|integer|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'min_wfo_full_time'], ['value' => $request->min_wfo_full_time]);
        Setting::updateOrCreate(['key' => 'min_wfo_part_time'], ['value' => $request->min_wfo_part_time]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Pengaturan Master Presence berhasil diperbarui.');
    }

    public function createPresence()
    {
        $employees = Employee::where('status', 'active')->orderBy('fullname')->get();

        $offices = OfficeLocation::orderBy('name')->get();

        return view('master-presences.create_presence', compact('employees', 'offices'));
    }

    public function storePresence(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'office_location_id' => 'required|exists:office_locations,id',
            'work_type' => 'required|string|in:WFO,WFH,WFA',
            'status' => 'required|string|in:present,late,absent,leave',
        ]);

        // get default coordinates from selected office
        $office = OfficeLocation::find($request->office_location_id);

        // create manual attendance record without photo or fingerprint validation
        Presence::updateOrCreate(
            [
                // search parameter - employee & date
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'check_in' => $request->status === 'absent' ? null : $request->date . ' 09:00:00',

                'check_out' => $request->status === 'absent' ? null : $request->date . ' 17:00:00',
                'latitude' => $office->latitude ?? '0.000000',
                'longitude' => $office->longitude ?? '0.000000',
                'check_out_latitude' => $office->latitude ?? '0.000000',
                'check_out_longitude' => $office->longitude ?? '0.000000',
                'office_location_id' => $request->office_location_id,
                'work_type' => $request->work_type,
                'status' => $request->status,
                'is_late' => 0,
                // use default system image
                'photo_path' => 'assets/images/default/admin-manual-presence.png',
                'notes' => 'Manually created/updated by admin (Correction/Override)',
            ],
        );

        return redirect()
            ->route('master-presences.index')
            ->with('success', 'Manual attendance record created successfully.');
    }
}
