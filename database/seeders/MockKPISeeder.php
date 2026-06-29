<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KPI;
use App\Models\Role;
use App\Models\Employee;
use App\Services\KPICalculationService;
use Illuminate\Support\Facades\DB;

class MockKPISeeder extends Seeder
{
    public function run()
    {
        // 1. Create the Master KPI
        $kpi = KPI::firstOrCreate(
            ['metric_category' => 'productivity', 'metric_key' => 'work_logs_count'],
            [
                'name' => 'Keaktifan Work Logs', 
                'code' => 'KPI-WL-01', 
                'category' => 'Productivity', 
                'unit' => 'Logs', 
                'status' => 'active', 
                'target_value' => 10, // Target set to 10 for Ita to get 100% since she has 10 logs
                'weight' => 20
            ]
        );

        // 2. Attach to all Roles so it becomes active for everyone
        $roles = Role::all();
        foreach ($roles as $role) {
            if (!$role->kpis()->where('kpi_id', $kpi->id)->exists()) {
                $role->kpis()->attach($kpi->id, ['target_value' => 10, 'weight' => 20]);
            }
        }

        // 3. Find Sasa Marinir and Recalculate her KPI for current period (June 2026)
        $ita = Employee::where('fullname', 'like', '%Sasa Marinir%')->first();
        if (!$ita) {
            $ita = Employee::first(); // Fallback to first employee
        }

        if ($ita) {
            $period = '2026-06';

            // Insert mock logs if she has none for June 2026
            $logCount = \App\Models\WorkLog::where('employee_id', $ita->id)
                ->where('log_date', 'like', '2026-06-%')
                ->count();
            if ($logCount == 0) {
                for ($i=1; $i<=10; $i++) {
                    \App\Models\WorkLog::create([
                        'employee_id' => $ita->id,
                        'log_date' => "2026-06-" . sprintf('%02d', $i),
                        'description' => "Mock activity $i"
                    ]);
                }
            }

            $service = new KPICalculationService($ita, $period);
            $metrics = $service->calculateAllKPIs();

            $actualValue = $metrics['productivity']['work_logs_count'] ?? 0;
            
            $achievement = ($actualValue / 10) * 100;
            $perf = KPICalculationService::getPerformanceLevel($achievement);
            $status = $achievement >= 75 ? 'achieved' : ($achievement >= 60 ? 'warning' : 'critical');
            
            DB::table('employee_kpi_records')->updateOrInsert(
                ['employee_id' => $ita->id, 'kpi_id' => $kpi->id, 'period' => $period],
                [
                    'target_value' => 10, 
                    'actual_value' => $actualValue, 
                    'composite_score' => round($achievement, 2), 
                    'status' => $status, 
                    'performance_level' => $perf, 
                    'submission_status' => 'approved', 
                    'updated_at' => now()
                ]
            );

            echo "Mock data injected successfully. Ita (ID: {$ita->id}) has {$actualValue} logs in {$period}.\n";
        } else {
            echo "Employee Ita not found.\n";
        }
    }
}
