<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

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

        return view('master-presences.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'enable_late_wfo' => 'required|in:0,1',
            'late_threshold_wfo' => 'nullable|integer|min:0',
            'enable_late_wfh' => 'required|in:0,1',
            'late_threshold_wfh' => 'nullable|integer|min:0',
            'enable_late_wfa' => 'required|in:0,1',
            'late_threshold_wfa' => 'nullable|integer|min:0',
            'overtime_rate_per_hour' => 'nullable|integer|min:0',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Pengaturan Master Presence berhasil diperbarui.');
    }
}