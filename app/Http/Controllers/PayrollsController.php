<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Setting;
use App\Models\OvertimeSubmission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PayrollsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Payroll::with('employee');

            if (!auth()->user()->isAdmin()) {
                $query->where('employee_id', auth()->user()->employee_id);
            }

            // Period filter
            if ($request->filled('filter_month')) {
                $query->where('period_month', $request->filter_month);
            }
            if ($request->filled('filter_year')) {
                $query->where('period_year', $request->filter_year);
            }
            if ($request->filled('filter_status')) {
                $query->where('status', $request->filter_status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('period', function ($row) {
                    return $row->period_label;
                })
                ->addColumn('employee_name', function ($row) {
                    $name = $row->employee?->fullname ?? '<em>Unknown</em>';
                    $nik = $row->employee?->nik ?? '-';
                    $npwp = $row->employee?->npwp ?? '-';
                    return '<div class="fw-bold">' . $name . '</div>' .
                           '<div class="text-muted small">NIK: ' . $nik . ' | NPWP: ' . $npwp . '</div>';
                })
                ->editColumn('net_salary', function ($row) {
                    return 'Rp ' . number_format($row->net_salary, 0, ',', '.');
                })
                ->editColumn('total_earnings', function ($row) {
                    return 'Rp ' . number_format($row->total_earnings, 0, ',', '.');
                })
                ->editColumn('total_deductions', function ($row) {
                    return 'Rp ' . number_format($row->total_deductions, 0, ',', '.');
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status_badge;
                })
                ->addColumn('action', function ($row) {
                    $btns = '<div class="btn-group btn-group-sm" role="group">';
                    $btns .= '<a href="' . route('payrolls.show', $row->id) . '" class="btn btn-outline-info" title="Lihat Slip"><i class="bi bi-eye"></i></a>';

                    if (in_array(session('role'), [\App\Constants\Roles::MASTER_ADMIN, 'HR Administrator'])) {
                        $btns .= '<a href="' . route('payrolls.edit', $row->id) . '" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>';
                        $csrf = csrf_token();
                        $btns .= '
                            <form action="' . route('payrolls.destroy', $row->id) . '" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="' . $csrf . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm(\'Yakin hapus data payroll ini?\')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        ';
                    }

                    $btns .= '</div>';
                    return $btns;
                })
                ->rawColumns(['action', 'status_badge', 'employee_name'])
                ->make(true);
        }

        return view('payrolls.index');
    }

    public function create()
    {
        $employees = Employee::orderBy('fullname')->get(['id', 'fullname', 'salary', 'emp_code']);
        $config = config('payroll');
        return view('payrolls.create', compact('employees', 'config'));
    }

    public function store(Request $request)
    {
        // Only Admin roles can create payroll
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2099',
            'salary' => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'performance_bonus' => 'nullable|numeric|min:0',
            'attendance_bonus' => 'nullable|numeric|min:0',
            'other_bonus' => 'nullable|numeric|min:0',
            'bonus_notes' => 'nullable|string',
            'working_days' => 'nullable|integer|min:0',
            'days_present' => 'nullable|integer|min:0',
            'late_count' => 'nullable|integer|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'absent_count' => 'nullable|integer|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'penalty_notes' => 'nullable|string',
            'bpjs_kes' => 'nullable|numeric|min:0',
            'bpjs_tk' => 'nullable|numeric|min:0',
            'pph21' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'deduction_notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,paid',
            'pay_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Zero out nulls
        $numericFields = [
            'transport_allowance', 'meal_allowance', 'position_allowance',
            'overtime_hours', 'overtime_amount',
            'performance_bonus', 'attendance_bonus', 'other_bonus',
            'late_deduction', 'absent_deduction', 'penalty_amount',
            'bpjs_kes', 'bpjs_tk', 'pph21', 'other_deduction',
        ];
        foreach ($numericFields as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }
        $validated['working_days'] = $validated['working_days'] ?? 0;
        $validated['days_present'] = $validated['days_present'] ?? 0;
        $validated['late_count'] = $validated['late_count'] ?? 0;
        $validated['absent_count'] = $validated['absent_count'] ?? 0;

        $payroll = new Payroll($validated);
        $payroll->calculateNetSalary();
        $payroll->save();

        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil dibuat.');
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee.department', 'employee.employeePositions.position')->findOrFail($id);

        // Access control: non-admin can only see own payroll
        if (!auth()->user()->isAdmin()) {
            if ($payroll->employee_id != auth()->user()->employee_id) {
                abort(403);
            }
        }

        return view('payrolls.show', compact('payroll'));
    }

    public function edit($id)
    {
        // Only Admin roles can edit payroll
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $payroll = Payroll::findOrFail($id);
        $employees = Employee::orderBy('fullname')->get(['id', 'fullname', 'salary', 'emp_code']);
        $config = config('payroll');
        return view('payrolls.edit', compact('payroll', 'employees', 'config'));
    }

    public function update(Request $request, $id)
    {
        // Only Admin roles can update payroll
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2099',
            'salary' => 'required|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'performance_bonus' => 'nullable|numeric|min:0',
            'attendance_bonus' => 'nullable|numeric|min:0',
            'other_bonus' => 'nullable|numeric|min:0',
            'bonus_notes' => 'nullable|string',
            'working_days' => 'nullable|integer|min:0',
            'days_present' => 'nullable|integer|min:0',
            'late_count' => 'nullable|integer|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'absent_count' => 'nullable|integer|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'penalty_notes' => 'nullable|string',
            'bpjs_kes' => 'nullable|numeric|min:0',
            'bpjs_tk' => 'nullable|numeric|min:0',
            'pph21' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'deduction_notes' => 'nullable|string',
            'status' => 'required|in:draft,approved,paid',
            'pay_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $numericFields = [
            'transport_allowance', 'meal_allowance', 'position_allowance',
            'overtime_hours', 'overtime_amount',
            'performance_bonus', 'attendance_bonus', 'other_bonus',
            'late_deduction', 'absent_deduction', 'penalty_amount',
            'bpjs_kes', 'bpjs_tk', 'pph21', 'other_deduction',
        ];
        foreach ($numericFields as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }
        $validated['working_days'] = $validated['working_days'] ?? 0;
        $validated['days_present'] = $validated['days_present'] ?? 0;
        $validated['late_count'] = $validated['late_count'] ?? 0;
        $validated['absent_count'] = $validated['absent_count'] ?? 0;

        $payroll = Payroll::findOrFail($id);
        $payroll->fill($validated);
        $payroll->calculateNetSalary();
        $payroll->save();

        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Only Admin roles can delete payroll
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $payroll = Payroll::findOrFail($id);
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Data payroll berhasil dihapus.');
    }

    /**
     * AJAX: Get attendance data for an employee in a given period.
     */
    public function getAttendanceData(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
        ]);

        $employeeId = $request->employee_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        $employee = Employee::findOrFail($employeeId);

        // Get presences for the period
        $presences = Presence::where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Count working days (weekdays in the month)
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        // Count days present (unique dates with check_in)
        $daysPresent = $presences->whereNotNull('check_in')->pluck('date')->unique()->count();

        // Count late arrivals
        $workStart = config('presence.work_start_time', '08:00');
        $lateThreshold = config('presence.late_threshold_minutes', 15);
        $lateLimit = Carbon::createFromFormat('H:i', $workStart)->addMinutes($lateThreshold);

        // $lateCount = 0;
        // foreach ($presences as $p) {
        //     if ($p->check_in) {
        //         $checkInTime = Carbon::parse($p->check_in);
        //         if ($checkInTime->format('H:i:s') > $lateLimit->format('H:i:s')) {
        //             $lateCount++;
        //         }
        //     }
        // }

        $lateCount = $presences->where('is_late', true)->count();

        // Absent days = working days that have passed - days present - approved leaves
        $today = Carbon::today();
        $effectiveEnd = $endDate->greaterThan($today) ? $today : $endDate;
        $passedWorkingDays = 0;
        $current = $startDate->copy();
        while ($current <= $effectiveEnd) {
            if (!$current->isWeekend()) {
                $passedWorkingDays++;
            }
            $current->addDay();
        }

        // Count approved leave days in the period
        $leaveCount = \App\Models\LeaveRequest::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->get()
            ->sum(function ($leave) use ($startDate, $endDate) {
                $start = Carbon::parse($leave->start_date)->greaterThan($startDate) ? Carbon::parse($leave->start_date) : $startDate;
                $end = Carbon::parse($leave->end_date)->lessThan($endDate) ? Carbon::parse($leave->end_date) : $endDate;
                $days = 0;
                $c = $start->copy();
                while ($c <= $end) {
                    if (!$c->isWeekend()) $days++;
                    $c->addDay();
                }
                return $days;
            });

        $absentCount = max(0, $passedWorkingDays - $daysPresent - $leaveCount);

        // Calculate deductions
        $baseSalary = (float) $employee->salary;
        // $dailySalary = $workingDays > 0 ? $baseSalary / $workingDays : 0;

        // $latePenalty = config('payroll.late_penalty_per_incident', 50000);
        // $absentMultiplier = config('payroll.absent_penalty_multiplier', 1.0);

        $lateDeduction = round($lateCount * ($baseSalary * 0.01));
        $absentDeduction = round($absentCount * ($baseSalary * 0.01));

        // BPJS calculations
        // BPJS Kes rules: 1% covers Employee + 1 Spouse + 3 Children (Total 5).
        // Each additional head adds 1%.
        $families = $employee->families;
        $spouseCount = $families->filter(fn($f) => in_array(strtolower($f->relation), ['pasangan', 'istri', 'suami', 'spouse']))->count();
        $childCount = $families->filter(fn($f) => in_array(strtolower($f->relation), ['anak', 'child']))->count();
        
        $extraHeads = max(0, $spouseCount - 1) + max(0, $childCount - 3);
        $bpjsKesRate = config('payroll.bpjs_kes_employee_rate', 0.01) + ($extraHeads * 0.01);

        $bpjsKes = round($baseSalary * $bpjsKesRate);
        $bpjsTk = round($baseSalary * config('payroll.bpjs_tk_employee_rate', 0.02));

        // Overtime 
        $overtimeRate = (int) Setting::getValue('overtime_rate_per_hour', 0);
        
        $totalApprovedOvertimeMinutes = OvertimeSubmission::where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('duration_minutes');

        $totalOvertimeHours = $totalApprovedOvertimeMinutes / 60;
        $overtimePay = round($totalOvertimeHours * $overtimeRate);

        return response()->json([
            'success' => true,
            'data' => [
                'base_salary' => $baseSalary,
                'working_days' => $workingDays,
                'days_present' => $daysPresent,
                'late_count' => $lateCount,
                'absent_count' => $absentCount,
                'leave_count' => $leaveCount,
                'late_deduction' => $lateDeduction,
                'absent_deduction' => round($absentDeduction),
                'bpjs_kes' => $bpjsKes,
                'bpjs_tk' => $bpjsTk,
                'transport_allowance' => config('payroll.default_transport_allowance', 500000),
                'meal_allowance' => config('payroll.default_meal_allowance', 500000),
                'overtime_hours' => round($totalOvertimeHours, 2),
                'overtime_amount' => $overtimePay,
            ],
        ]);
    }

    /**
     * AJAX: Get employee salary data.
     */
    public function getEmployeeData(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id']);
        $employee = Employee::findOrFail($request->employee_id);

        return response()->json([
            'success' => true,
            'data' => [
                'salary' => (float) $employee->salary,
                'fullname' => $employee->fullname,
                'emp_code' => $employee->emp_code,
            ],
        ]);
    }
}
