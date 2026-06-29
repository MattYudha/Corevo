<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\EmployeeKPIRecord;

class DeleteMockSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('email', 'LIKE', '%@demo.com')->get();
        foreach ($users as $u) {
            if ($u->employee) {
                EmployeeKPIRecord::where('employee_id', $u->employee->id)->delete();
                $u->employee->forceDelete();
            }
            $u->delete();
        }
        echo "Deleted all mock users.\n";
    }
}
