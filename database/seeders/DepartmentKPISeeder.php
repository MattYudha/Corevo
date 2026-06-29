<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KPI;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DepartmentKPISeeder extends Seeder
{
    public function run()
    {
        $kpis = [
            // Admin
            [
                'name' => 'Pengelolaan Surat & Dokumen',
                'code' => 'KPI-DPT-01',
                'category' => 'Productivity',
                'unit' => 'Dokumen',
                'status' => 'active',
                'target_value' => 50,
                'weight' => 20,
                'metric_category' => 'department',
                'metric_key' => 'admin_documents_processed',
                'role_keyword' => 'Admin'
            ],
            // Finance
            [
                'name' => 'Pengelolaan Transaksi Keuangan',
                'code' => 'KPI-DPT-02',
                'category' => 'Productivity',
                'unit' => 'Transaksi',
                'status' => 'active',
                'target_value' => 50,
                'weight' => 20,
                'metric_category' => 'department',
                'metric_key' => 'finance_transactions_count',
                'role_keyword' => 'Finance'
            ],
            // Sales
            [
                'name' => 'Klien Baru & Closing',
                'code' => 'KPI-DPT-03',
                'category' => 'Productivity',
                'unit' => 'Klien',
                'status' => 'active',
                'target_value' => 10,
                'weight' => 30,
                'metric_category' => 'department',
                'metric_key' => 'sales_contacts_count',
                'role_keyword' => 'Sales'
            ],
            // IT / Developer
            [
                'name' => 'Penyelesaian Project IT',
                'code' => 'KPI-DPT-04',
                'category' => 'Productivity',
                'unit' => 'Project',
                'status' => 'active',
                'target_value' => 5,
                'weight' => 25,
                'metric_category' => 'department',
                'metric_key' => 'it_projects_completed',
                'role_keyword' => 'Developer'
            ],
        ];

        echo "Inserting Department Specific KPIs...\n";

        foreach ($kpis as $kpiData) {
            $keyword = $kpiData['role_keyword'];
            unset($kpiData['role_keyword']);

            $kpi = KPI::firstOrCreate(
                [
                    'metric_category' => $kpiData['metric_category'], 
                    'metric_key' => $kpiData['metric_key']
                ],
                $kpiData
            );

            // Assign specifically to role matching keyword
            $roles = Role::where('title', 'like', "%{$keyword}%")->get();
            if ($roles->isEmpty()) {
                // Fallback to Manager so we have some mapping
                $roles = Role::where('title', 'like', '%Manager%')->get();
            }

            foreach ($roles as $role) {
                if (!$role->kpis()->where('kpi_id', $kpi->id)->exists()) {
                    $role->kpis()->attach($kpi->id, [
                        'target_value' => $kpiData['target_value'], 
                        'weight' => $kpiData['weight']
                    ]);
                }
            }
        }
        
        echo "Successfully injected Department Specific KPIs.\n";
    }
}
