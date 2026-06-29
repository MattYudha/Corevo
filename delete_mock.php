<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::where('email', 'LIKE', '%@demo.com')->get();
foreach ($users as $u) {
    if ($u->employee) {
        App\Models\EmployeeKPIRecord::where('employee_id', $u->employee->id)->delete();
        $u->employee->forceDelete();
    }
    $u->delete();
}
echo "Deleted old mock users.\n";
