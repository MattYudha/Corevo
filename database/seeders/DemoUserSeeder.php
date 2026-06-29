<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoUserSeeder extends Seeder
{
    public function run()
    {
        $rolesToCreate = [
            ['email' => 'it1@demo.com', 'name' => 'Budi Developer', 'role' => 'Developer', 'dept' => 'IT'],
            ['email' => 'sales1@demo.com', 'name' => 'Eka Sales', 'role' => 'Sales', 'dept' => 'Marketing'],
        ];

        foreach ($rolesToCreate as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Password123!'),
                ]
            );

            $role = Role::firstOrCreate(['title' => $data['role']]);

            $employee = Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'fullname' => $data['name'],
                    'emp_code' => 'EMP-' . strtoupper(substr(md5($data['email']), 0, 6)),
                    'nik' => '327' . rand(1000000000000, 9999999999999),
                    'phone_number' => '081' . rand(100000000, 999999999),
                    'address' => 'Jl. Demo No. ' . rand(1, 100),
                    'role_id' => $role->id,
                    'department_id' => 1,
                    'status' => 'active',
                    'employee_status' => 'permanent',
                    'npwp' => '00.' . rand(100, 999) . '.' . rand(100, 999) . '.7-001.000',
                    'hire_date' => now()->subYears(1)->format('Y-m-d'),
                    'salary' => rand(5, 15) * 1000000,
                ]
            );

            $user->update(['employee_id' => $employee->id]);
        }

        echo "Created 10 mock users.\n";
        
        Artisan::call('db:seed', ['--class' => 'DepartmentKPISeeder']);
        
        Artisan::call('kpi:seed');
        echo "KPI Seed ran successfully.\n";
        
        DB::table('employee_kpi_records')->update(['submission_status' => 'approved']);
        echo "All KPIs marked as approved for reports.\n";
    }
}
