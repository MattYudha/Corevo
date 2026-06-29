<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Task;
use App\Models\LeaveRequest;
use App\Models\WorkLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class OperationalMockSeeder extends Seeder
{
    public function run()
    {
        // Force the period to be June 2026 to match the demo UI period
        $periodStr = '2026-06';
        $startDate = Carbon::createFromFormat('Y-m', $periodStr)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $employees = Employee::where('status', 'active')->get();

        foreach ($employees as $employee) {
            echo "Seeding operational data for: {$employee->fullname}\n";
            
            // 1. Presences (simulate ~18 days present, maybe 1 late, 1 early checkout)
            $workingDays = CarbonPeriod::create($startDate, $endDate)->filter(fn($d) => $d->isWeekday());
            $dayCount = 0;
            
            foreach ($workingDays as $date) {
                $dayCount++;
                if ($dayCount > 18) break; // Limit to 18 attendances
                
                $dateStr = $date->format('Y-m-d');
                $checkIn = "{$dateStr} 08:00:00";
                $checkOut = "{$dateStr} 17:00:00";
                
                // Make the first day late
                if ($dayCount === 1) $checkIn = "{$dateStr} 08:45:00";
                // Make the second day early checkout
                if ($dayCount === 2) $checkOut = "{$dateStr} 16:00:00";
                
                Presence::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $dateStr,
                    ],
                    [
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => 'present',
                        'is_late' => $dayCount === 1,
                    ]
                );
            }

            // 2. Tasks (simulate 5 completed tasks)
            for ($i = 1; $i <= 5; $i++) {
                Task::firstOrCreate(
                    [
                        'title' => "Mock Task {$i} for {$employee->fullname}",
                        'assigned_to' => $employee->id,
                    ],
                    [
                        'description' => "This is a mock task generated for demo purposes.",
                        'due_date' => $startDate->copy()->addDays($i * 3)->format('Y-m-d H:i:s'),
                        'status' => 'completed',
                        'completed_at' => $startDate->copy()->addDays($i * 3)->subHours(2)->format('Y-m-d H:i:s'), // completed early/on-time
                        'quality_rating' => rand(3, 5), // Random quality rating between 3 and 5
                    ]
                );
            }

            // 3. Work Logs (simulate 15 work logs)
            for ($i = 1; $i <= 15; $i++) {
                WorkLog::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'log_date' => $startDate->copy()->addDays($i)->format('Y-m-d'),
                        'description' => "Mock activity log {$i}",
                    ]
                );
            }

            // 4. Leave Requests (simulate 1 leave request for 2 days)
            LeaveRequest::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'start_date' => $startDate->copy()->addDays(20)->format('Y-m-d'),
                ],
                [
                    'end_date' => $startDate->copy()->addDays(21)->format('Y-m-d'),
                    'leave_type' => 'sick',
                    'status' => 'approved'
                ]
            );
            
            // 5. Leave Balances
            \App\Models\LeaveBalance::firstOrCreate(
                ['employee_id' => $employee->id, 'year' => $startDate->year, 'leave_type' => 'annual'],
                ['entitlement' => 12, 'taken' => 2, 'balance' => 10]
            );
        }

        echo "Operational mock data injected successfully for period {$periodStr}.\n";
    }
}
