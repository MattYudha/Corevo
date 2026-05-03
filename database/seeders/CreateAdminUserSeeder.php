<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Pastikan department ada ─────────────────────────────
        $department = DB::table('departments')->whereNull('deleted_at')->first();
        if (!$department) {
            $deptId = DB::table('departments')->insertGetId([
                'name'       => 'Management',
                'status'     => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $deptId = $department->id;
        }

        // ── 2. Pastikan role ada ───────────────────────────────────
        $role = DB::table('roles')->whereNull('deleted_at')->first();
        if (!$role) {
            $roleId = DB::table('roles')->insertGetId([
                'title'      => 'Administrator',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $roleId = $role->id;
        }

        // ── 3. Skip jika admin sudah ada ──────────────────────────
        if (DB::table('users')->where('email', 'admin@example.com')->exists()) {
            $this->command->info('Admin user already exists, skipping.');
            return;
        }

        // ── 4. Buat employee record ────────────────────────────────
        $employeeId = DB::table('employees')->insertGetId([
            'nik'             => 'ADMIN001',
            'npwp'            => '00.000.000.0-000.001',
            'fullname'        => 'System Administrator',
            'email'           => 'admin@example.com',
            'phone_number'    => '000-000-0000',
            'address'         => 'Head Office',
            'birth_date'      => '1990-01-01',
            'hire_date'       => Carbon::now(),
            'department_id'   => $deptId,
            'role_id'         => $roleId,
            'supervisor_id'   => null,
            'status'          => 'active',
            'employee_status' => 'permanent',
            'salary'          => 0,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);

        // ── 5. Buat user account ───────────────────────────────────
        DB::table('users')->insert([
            'name'        => 'Administrator',
            'email'       => 'admin@example.com',
            'password'    => Hash::make('Password123!'),
            'employee_id' => $employeeId,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email   : admin@example.com');
        $this->command->info('Password: Password123!');
    }
}
