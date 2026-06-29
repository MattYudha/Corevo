<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KPI;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class GranularKPISeeder extends Seeder
{
    public function run()
    {
        $granularKPIs = [
            // Attendance
            [
                'name' => 'Kepatuhan Check-in',
                'code' => 'KPI-ATT-01',
                'category' => 'Attendance',
                'unit' => '%',
                'status' => 'active',
                'target_value' => 100,
                'weight' => 15,
                'metric_category' => 'attendance',
                'metric_key' => 'punctuality',
            ],
            [
                'name' => 'Frekuensi Keterlambatan',
                'code' => 'KPI-ATT-02',
                'category' => 'Attendance',
                'unit' => 'Hari',
                'status' => 'active',
                'target_value' => 0, // Target 0 days late
                'weight' => 10,
                'metric_category' => 'attendance',
                'metric_key' => 'late_count',
            ],
            [
                'name' => 'Kepatuhan Jam Pulang (Early Checkout)',
                'code' => 'KPI-ATT-03',
                'category' => 'Attendance',
                'unit' => '%',
                'status' => 'active',
                'target_value' => 0, // Target 0% early checkout
                'weight' => 10,
                'metric_category' => 'attendance',
                'metric_key' => 'early_checkout_rate',
            ],
            
            // Productivity (Activity)
            [
                'name' => 'Keaktifan Work Logs',
                'code' => 'KPI-PRD-01',
                'category' => 'Productivity',
                'unit' => 'Logs',
                'status' => 'active',
                'target_value' => 20, // 20 logs standard
                'weight' => 15,
                'metric_category' => 'productivity',
                'metric_key' => 'work_logs_count',
            ],
            [
                'name' => 'Penyelesaian Tugas Tepat Waktu',
                'code' => 'KPI-PRD-02',
                'category' => 'Productivity',
                'unit' => '%',
                'status' => 'active',
                'target_value' => 100, // 100% on time
                'weight' => 25,
                'metric_category' => 'productivity',
                'metric_key' => 'on_time_delivery_rate',
            ],
        ];

        echo "Inserting granular KPIs...\n";

        foreach ($granularKPIs as $kpiData) {
            $kpi = KPI::firstOrCreate(
                [
                    'metric_category' => $kpiData['metric_category'], 
                    'metric_key' => $kpiData['metric_key']
                ],
                $kpiData
            );

            // Attach to all Roles so it becomes active for everyone
            $roles = Role::all();
            foreach ($roles as $role) {
                if (!$role->kpis()->where('kpi_id', $kpi->id)->exists()) {
                    $role->kpis()->attach($kpi->id, [
                        'target_value' => $kpiData['target_value'], 
                        'weight' => $kpiData['weight']
                    ]);
                }
            }
        }
        
        echo "Successfully injected 5 granular KPIs for Best Employee Evaluation.\n";
    }
}
