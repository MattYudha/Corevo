<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeKPIRecord;
use App\Models\KPI;
use App\Models\PerformanceReview;
use App\Models\Incident;
use App\Models\KPIRecordProxy;
use App\Services\KPICalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Presence;
use Carbon\Carbon;

class KPIController extends Controller
{
    /**
     * Show KPI dashboard for current user
     */
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked to employee record.');
        }

        $period = now()->format('Y-m');
        try {
            // Use Eloquent with eager loading
            $records = EmployeeKPIRecord::with('kpi')
                ->where('employee_id', $employee->id)
                ->where('period', $period)
                ->orderBy('id')
                ->get();
            
            $kpiRecords = $records->map(function($record) {
                return new KPIRecordProxy($record, $record->kpi);
            });
        } catch (\Exception $e) {
            // Table may not exist yet
            $kpiRecords = collect([]);
        }

        $summary = KPICalculationService::calculateWeightedScore($kpiRecords);
        $compositeScore = $summary['score'];
        $performanceLevel = $summary['level'];
        $kpisByCategory = $kpiRecords->groupBy(function($r) { return $r->kpi->category; });
        
        try {
            $incidents = Incident::where('employee_id', $employee->id)
                ->where('status', '!=', 'resolved')
                ->orderByDesc('incident_date')
                ->get();
        } catch (\Exception $e) {
            $incidents = collect([]);
        }

        return view('kpi.dashboard', compact('employee', 'period', 'kpiRecords', 'compositeScore', 'performanceLevel', 'kpisByCategory', 'incidents'));
    }

    /**
     * Show employee KPI report
     */
    public function show($id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);

        if (($user->employee?->id ?? null) !== $employee->id && !\App\Constants\Roles::isAdmin(session('role')) && ($user->employee?->role?->title ?? '') !== \App\Constants\Roles::MANAGER_UNIT_HEAD) {
            abort(403, 'Unauthorized');
        }

        $period = request('period', now()->format('Y-m'));
        try {
            // Use Eloquent with eager loading
            $records = EmployeeKPIRecord::with('kpi')
                ->where('employee_id', $employee->id)
                ->where('period', $period)
                ->orderBy('id')
                ->get();
            
            $kpiRecords = $records->map(function($record) {
                return new KPIRecordProxy($record, $record->kpi);
            });
        } catch (\Exception $e) {
            $kpiRecords = collect([]);
        }

        $summary = KPICalculationService::calculateWeightedScore($kpiRecords);
        $compositeScore = $summary['score'];
        $performanceLevel = $summary['level'];
        $kpisByCategory = $kpiRecords->groupBy(function($r) { return $r->kpi->category; });

        try {
            $performanceReview = PerformanceReview::where('employee_id', $id)
                ->where('period', $period)
                ->first();
        } catch (\Exception $e) {
            $performanceReview = null;
        }

        return view('kpi.show', compact('employee', 'period', 'kpiRecords', 'kpisByCategory', 'performanceReview', 'compositeScore', 'performanceLevel'));
    }

    /**
     * Show team KPI
     */
    public function team()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked to employee.');
        }

        $period = request('period', now()->format('Y-m'));
        $teamMembers = Employee::where('supervisor_id', $employee->id)->get();

        $teamKPIs = $teamMembers->map(function($member) use ($period) {
            $records = EmployeeKPIRecord::with('kpi')
                ->where('employee_id', $member->id)
                ->where('period', $period)
                ->get();
            
            $proxyRecords = $records->map(function($r) { return new KPIRecordProxy($r, $r->kpi); });
            $summary = KPICalculationService::calculateWeightedScore($proxyRecords);

            return [
                'employee' => $member,
                'composite_score' => $summary['score'],
                'performance_level' => $summary['level'],
            ];
        })->sortByDesc('composite_score');

        return view('kpi.team', compact('teamMembers', 'teamKPIs', 'period'));
    }

    /**
     * Show department KPI summary
     */
    public function department()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked.');
        }

        $period = request('period', now()->format('Y-m'));
        $deptEmployees = Employee::where('department_id', $employee->department_id)->get();

        $deptKPIs = $deptEmployees->map(function($emp) use ($period) {
            $records = EmployeeKPIRecord::with('kpi')
                ->where('employee_id', $emp->id)
                ->where('period', $period)
                ->get();
            
            $proxyRecords = $records->map(function($r) { return new KPIRecordProxy($r, $r->kpi); });
            $summary = KPICalculationService::calculateWeightedScore($proxyRecords);

            return [
                'employee' => $emp,
                'composite_score' => $summary['score'],
                'performance_level' => $summary['level'],
            ];
        })->sortByDesc('composite_score');

        $avgScore = $deptKPIs->avg('composite_score');

        return view('kpi.department', compact('deptEmployees', 'deptKPIs', 'avgScore', 'period'));
    }

    /**
     * Submit KPI for supervisor approval
     */
    public function submit(Request $request, $employeeId)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($employeeId);

        // Verify user owns this employee record
        if ($user->employee->id !== $employee->id) {
            abort(403, 'You can only submit your own KPI.');
        }

        $period = $request->input('period', now()->format('Y-m'));

        // Update all KPI records for this employee/period to submitted
        \DB::table('employee_kpi_records')
            ->where('employee_id', $employee->id)
            ->where('period', $period)
            ->update([
                'submission_status' => 'submitted',
                'submitted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('kpi.dashboard')
            ->with('success', 'KPI berhasil disubmit untuk review oleh atasan.');
    }

    /**
     * Update individual KPI record by employee
     */
    public function updateRecord(Request $request, $recordId)
    {
        $user = Auth::user();
        $record = EmployeeKPIRecord::findOrFail($recordId);

        // Authorization: Employee on their own record
        if ($user->employee->id !== $record->employee_id) {
            abort(403, 'Unauthorized');
        }

        // Only allow updates if in draft or rejected
        if (!in_array($record->submission_status, ['draft', 'rejected'])) {
            return redirect()->back()->with('error', 'KPI sudah disubmit dan tidak bisa diubah.');
        }

        $request->validate([
            'actual_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'notes' => $request->input('notes'),
            'updated_at' => now(),
        ];

        // Only allow updating actual_value if it's not an auto-calculated KPI
        if ($record->kpi && is_null($record->kpi->metric_category)) {
            if ($request->has('actual_value')) {
                $data['actual_value'] = $request->input('actual_value');
                
                // Recalculate achievement and performance level
                $target = $record->target_value > 0 ? $record->target_value : 100;
                $achievement = ($data['actual_value'] / $target) * 100;
                $data['composite_score'] = round($achievement, 2);
                $data['performance_level'] = KPICalculationService::getPerformanceLevel($achievement);
                
                // Status mapping
                if ($achievement >= 90) {
                    $data['status'] = 'achieved';
                } elseif ($achievement >= 75) {
                    $data['status'] = 'achieved';
                } elseif ($achievement >= 60) {
                    $data['status'] = 'warning';
                } else {
                    $data['status'] = 'critical';
                }
            }
        }

        $record->update($data);

        return redirect()->back()->with('success', 'KPI berhasil diperbarui.');
    }

    /**
     * Show pending KPI approvals for manager
     */
    public function pendingApprovals()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'User not linked to employee.');
        }

        $period = request('period', now()->format('Y-m'));

        // Get subordinates
        $subordinates = Employee::where('supervisor_id', $employee->id)->pluck('id');

        // Get pending KPI records grouped by employee
        $employeesWithPending = Employee::whereIn('id', $subordinates)
            ->whereHas('kpiRecords', function($query) use ($period) {
                $query->where('period', $period)->where('submission_status', 'submitted');
            })
            ->with(['kpiRecords' => function($query) use ($period) {
                $query->where('period', $period)->where('submission_status', 'submitted')->with('kpi');
            }])
            ->get();

        $pendingKPIs = $employeesWithPending->map(function($emp) use ($period) {
            $proxyRecords = $emp->kpiRecords->map(function($r) { return new KPIRecordProxy($r, $r->kpi); });
            $summary = KPICalculationService::calculateWeightedScore($proxyRecords);
            
            // Find the most recent submission time from records
            $submittedAt = $emp->kpiRecords->max('submitted_at');

            return (object) [
                'employee_id' => $emp->id,
                'fullname' => $emp->fullname,
                'period' => $period,
                'submitted_at' => $submittedAt,
                'composite_score' => $summary['score'],
                'performance_level' => $summary['level'],
            ];
        });

        return view('kpi.pending', compact('pendingKPIs', 'period'));
    }

    /**
     * Approve subordinate's KPI
     */
    public function approve(Request $request, $employeeId)
    {
        $user = Auth::user();
        $manager = $user->employee;
        $employee = Employee::findOrFail($employeeId);

        // Verify this employee reports to current user
        if ($employee->supervisor_id !== $manager->id) {
            abort(403, 'Anda bukan atasan langsung karyawan ini.');
        }

        $period = $request->input('period', now()->format('Y-m'));

        // Update all KPI records for this employee/period to approved
        \DB::table('employee_kpi_records')
            ->where('employee_id', $employee->id)
            ->where('period', $period)
            ->where('submission_status', 'submitted')
            ->update([
                'submission_status' => 'approved',
                'reviewed_by' => $manager->id,
                'reviewed_at' => now(),
                'reviewer_notes' => $request->input('notes'),
                'updated_at' => now(),
            ]);

        return redirect()->route('kpi.pending')
            ->with('success', 'KPI ' . $employee->fullname . ' berhasil disetujui.');
    }

    /**
     * Reject subordinate's KPI
     */
    public function reject(Request $request, $employeeId)
    {
        $user = Auth::user();
        $manager = $user->employee;
        $employee = Employee::findOrFail($employeeId);

        // Verify this employee reports to current user
        if ($employee->supervisor_id !== $manager->id) {
            abort(403, 'Anda bukan atasan langsung karyawan ini.');
        }

        $period = $request->input('period', now()->format('Y-m'));
        $notes = $request->input('notes', 'Ditolak tanpa catatan.');

        // Update all KPI records for this employee/period to rejected
        \DB::table('employee_kpi_records')
            ->where('employee_id', $employee->id)
            ->where('period', $period)
            ->where('submission_status', 'submitted')
            ->update([
                'submission_status' => 'rejected',
                'reviewed_by' => $manager->id,
                'reviewed_at' => now(),
                'reviewer_notes' => $notes,
                'updated_at' => now(),
            ]);

        return redirect()->route('kpi.pending')
            ->with('success', 'KPI ' . $employee->fullname . ' ditolak.');
    }

    /**
     * Recalculate KPIs for an employee
     */
    public function recalculate(Request $request, $id)
    {
        $user = Auth::user();
        
        // Authorization check
        if (!\App\Constants\Roles::isAdmin($user->employee?->role?->title ?? '')) {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::findOrFail($id);
        $period = $request->input('period', now()->format('Y-m'));

        try {
            // 1. Calculate Metrics
            $service = new KPICalculationService($employee, $period);
            $metrics = $service->calculateAllKPIs();

            // 2. Dynamic KPI Mapping using Role-based configuration
            $kpis = $employee->role->kpis()
                ->whereNotNull('metric_category')
                ->whereNotNull('metric_key')
                ->get();

            foreach ($kpis as $kpi) {
                // Get actual value from calculated metrics using dynamic mapping
                $actualValue = $metrics[$kpi->metric_category][$kpi->metric_key] ?? 0;
                
                // Use pivot values for target and weight if available, fallback to KPI defaults
                $target = $kpi->pivot->target_value ?? ($kpi->target_value > 0 ? $kpi->target_value : 100);
                $weight = $kpi->pivot->weight ?? ($kpi->weight ?? 0);
                
                $achievement = ($actualValue / $target) * 100;
                $perf = KPICalculationService::getPerformanceLevel($achievement);
                
                // Status mapping based on achievement
                if ($achievement >= 90) {
                    $status = 'achieved';
                } elseif ($achievement >= 75) {
                    $status = 'achieved';
                } elseif ($achievement >= 60) {
                    $status = 'warning';
                } else {
                    $status = 'critical';
                }

                \DB::table('employee_kpi_records')->updateOrInsert(
                    [
                        'employee_id' => $employee->id,
                        'kpi_id' => $kpi->id,
                        'period' => $period
                    ],
                    [
                        'actual_value' => $actualValue,
                        'target_value' => $target,
                        'composite_score' => round($achievement, 2),
                        'status' => $status,
                        'performance_level' => $perf,
                        'updated_at' => now(),
                    ]
                );
            }

            return redirect()->back()->with('success', 'KPI berhasil dikalkulasi ulang.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengkalkulasi KPI: ' . $e->getMessage());
        }
    }

    /**
     * Show historical performance trend for an employee
     */
    public function trend(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);

        // Authorization: User can view their own trend, or managers/HR Administrator can view anyone's
        if (($user->employee?->id ?? null) !== $employee->id && !\App\Constants\Roles::isAdmin(session('role')) && ($user->employee?->role?->title ?? '') !== \App\Constants\Roles::MANAGER_UNIT_HEAD) {
            abort(403, 'Unauthorized');
        }

        $months = (int) $request->input('months', 6); // Default 6 months, max 12
        $months = min($months, 12);
        
        $trendData = [];
        $categories = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $period = now()->subMonths($i)->format('Y-m');
            
            // Get KPI record for this period
            $record = EmployeeKPIRecord::where('employee_id', $employee->id)
                ->where('period', $period)
                ->first();
            
            // Get category breakdown
            $categoryScores = [];
            if ($record) {
                $allRecords = EmployeeKPIRecord::with('kpi')
                    ->where('employee_id', $employee->id)
                    ->where('period', $period)
                    ->get();
                
                foreach ($allRecords as $r) {
                    if ($r->kpi) {
                        $category = $r->kpi->category;
                        if (!isset($categoryScores[$category])) {
                            $categoryScores[$category] = [];
                        }
                        $categoryScores[$category][] = $r->composite_score;
                    }
                }
                
                // Calculate averages
                foreach ($categoryScores as $cat => $scores) {
                    $categoryScores[$cat] = round(array_sum($scores) / count($scores), 2);
                }
            }

            $trendData[] = [
                'period' => $period,
                'period_label' => now()->subMonths($i)->format('M Y'),
                'composite_score' => $record?->composite_score ?? 0,
                'performance_level' => $record?->performance_level ?? 'na',
                'category_scores' => $categoryScores,
            ];

            // Collect unique categories for legend
            foreach (array_keys($categoryScores) as $cat) {
                if (!in_array($cat, $categories)) {
                    $categories[] = $cat;
                }
            }
        }

        
        /* ================= DASHBOARD STATS INTEGRATION ================= */
        
        // Identify if we are viewing as Manager / Unit Head (Team) or Employee (Individual)
        // If the logged in user is looking at their own record, treat as Individual/Employee view
        // UNLESS they are a manager looking at their own trend? No, request says "if manager show team report, if employee show own data".
        // But here we are on a specific employee's trend page: /kpi/trend/{id}
        // So the context is strictly about THIS employee {id}. 
        // HOWEVER, the user asked: "tambahkan halaman ini pada halaman dashboard... dan apabila dia manager tampilkan report dari team masing masing, atau jika dia karyawan, tampilkan data milik dia sendiri"
        // This implies the widgets added to THIS page should reflect the USER'S scope (or the target employee's scope if we treat the target as the 'manager').
        // Let's assume if the target employee ($employee) is a Supervisor, we show their TEAM'S stats.
        // If the target employee is a regular employee, we show THEIR stats.
        
        $isManager = Employee::where('supervisor_id', $employee->id)->exists();
        $targetEmployeeIds = $isManager 
            ? Employee::where('supervisor_id', $employee->id)->pluck('id')->toArray()
            : [$employee->id];

        // 1. Counts
        $departmentCount = Department::count(); // Global context usually
        $employeeCount   = $isManager ? count($targetEmployeeIds) : 1;
        
        $presenceCount = Presence::whereIn('employee_id', $targetEmployeeIds)->count();
        $payrollCount  = Payroll::whereIn('employee_id', $targetEmployeeIds)->count();

        // 2. Presence Chart (Last 12 months)
        $presenceRaw = Presence::selectRaw('MONTH(date) as month, COUNT(*) as total')
            ->whereIn('employee_id', $targetEmployeeIds)
            ->where('date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $presenceLabels = [];
        $presenceData   = [];
        foreach ($presenceRaw as $row) {
            // Use current year when creating Carbon instance
            $presenceLabels[] = Carbon::create(now()->year, $row->month, 1)->format('F');
            $presenceData[]   = $row->total;
        }

        // 3. Payroll Chart (Last 12 months)
        $payrollRaw = Payroll::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereIn('employee_id', $targetEmployeeIds)
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $payrollLabels = [];
        $payrollData   = [];
        foreach ($payrollRaw as $row) {
            // Use current year when creating Carbon instance
            $payrollLabels[] = Carbon::create(now()->year, $row->month, 1)->format('F');
            $payrollData[]   = $row->total;
        }

        return view('kpi.trend', compact(
            'employee', 'trendData', 'months', 'categories',
            'departmentCount', 'employeeCount', 'presenceCount', 'payrollCount',
            'presenceLabels', 'presenceData', 'payrollLabels', 'payrollData'
        ));
    }

    /**
     * Admin: Show edit form for a specific KPI record.
     */
    public function adminEdit(Request $request, $employeeId, $recordId)
    {
        if (!\App\Constants\Roles::isAdmin(session('role'))) {
            abort(403, 'Unauthorized');
        }

        $employee = Employee::findOrFail($employeeId);
        $record   = EmployeeKPIRecord::with('kpi')->findOrFail($recordId);

        // Make sure the record belongs to the employee
        if ($record->employee_id != $employeeId) {
            abort(404);
        }

        return view('kpi.admin-edit', compact('employee', 'record'));
    }

    /**
     * Admin: Save manual KPI update for a specific record.
     */
    public function adminUpdate(Request $request, $employeeId, $recordId)
    {
        if (!\App\Constants\Roles::isAdmin(session('role'))) {
            abort(403, 'Unauthorized');
        }

        $record = EmployeeKPIRecord::findOrFail($recordId);

        if ($record->employee_id != $employeeId) {
            abort(404);
        }

        $validated = $request->validate([
            'actual_value'     => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:1000',
            'status'           => 'required|in:achieved,warning,critical',
            'performance_level'=> 'required|in:excellent,good,satisfactory,needs_improvement,poor',
        ]);

        // Recalculate composite score from actual vs target
        $target = $record->target_value > 0 ? $record->target_value : 100;
        $achievement = ($validated['actual_value'] / $target) * 100;

        $record->update([
            'actual_value'      => $validated['actual_value'],
            'notes'             => $validated['notes'],
            'status'            => $validated['status'],
            'performance_level' => $validated['performance_level'],
            'composite_score'   => round($achievement, 2),
            'submission_status' => 'approved', // Admin edit is auto-approved
            'reviewed_by'       => session('employee_id'),
            'reviewed_at'       => now(),
            'reviewer_notes'    => '[Admin Manual Edit] ' . ($validated['notes'] ?? ''),
        ]);

        return redirect()->route('kpi.show', $employeeId)
            ->with('success', 'KPI record berhasil diperbarui oleh admin.');
    }
}
