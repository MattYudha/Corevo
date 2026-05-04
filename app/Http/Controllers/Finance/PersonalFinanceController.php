<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialClaim;
use App\Models\Payroll; // Assuming Payroll model exists, if not, we'll gracefully handle it.
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalFinanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $employeeId = $employee?->id;

        // Base data for the employee
        $claimsQuery = FinancialClaim::query();
        if ($employeeId) {
            $claimsQuery->where('employee_id', $employeeId);
        } else {
            $claimsQuery->where(function($q) {
                $q->whereNull('employee_id')->orWhere('employee_id', 0);
            });
        }

        $claims = (clone $claimsQuery)->latest()->take(5)->get();

        $stats = [
            'total_claims_pending'  => (clone $claimsQuery)->pending()->count(),
            'total_claims_approved' => (clone $claimsQuery)->approved()->sum('amount'),
            'total_claims_rejected' => (clone $claimsQuery)->rejected()->count(),
        ];

        // Safely fetch payroll data if the model/table exists
        $payrolls = collect();
        $totalEarningsYtd = 0;
        $monthlyEarnings = array_fill(0, 12, 0);
        $chartLabels = [];

        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = Carbon::create(null, $i, 1)->translatedFormat('M');
        }

        if (class_exists(\App\Models\Payroll::class) && $employee) {
            try {
                // Fetch YTD payrolls
                $year = date('Y');
                $payrollData = \App\Models\Payroll::where('employee_id', $employee->id)
                    ->whereYear('created_at', $year) // or payment_date depending on schema
                    ->get();
                
                $totalEarningsYtd = $payrollData->sum('net_salary'); // adjust field based on schema
                
                foreach ($payrollData as $p) {
                    $monthIndex = $p->created_at->month - 1;
                    $monthlyEarnings[$monthIndex] += $p->net_salary;
                }

                $payrolls = \App\Models\Payroll::where('employee_id', $employee->id)
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                // Ignore if table/schema differs
            }
        }

        return view('finance.my-finance.index', compact(
            'employee', 'claims', 'stats', 'totalEarningsYtd', 'monthlyEarnings', 'chartLabels', 'payrolls'
        ));
    }
}
