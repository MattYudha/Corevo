<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an employee record for the admin
        $employee = Employee::create([
            'fullname' => 'System Administrator',
            'email' => 'admin@example.com',
            'phone_number' => '000-000-0000',
            'address' => 'Head Office',
            'birth_date' => Carbon::parse('1990-01-01'),
            'hire_date' => Carbon::now(),
            'department_id' => 1, // assume HR exists from other seeders
            'role_id' => 1, // assume Manager/Developer role exists
            'supervisor_id' => null,
            'status' => 'active',
            'salary' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create the linked user account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123!'),
            'employee_id' => $employee->id,
        ]);
    }
}
