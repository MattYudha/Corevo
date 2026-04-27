<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // employees table sync
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'emp_code')) {
                $table->string('emp_code')->after('id')->nullable();
            }
            if (!Schema::hasColumn('employees', 'nik')) {
                $table->string('nik')->after('emp_code')->nullable();
            }
            if (!Schema::hasColumn('employees', 'npwp')) {
                $table->string('npwp')->after('email')->nullable();
            }
            if (!Schema::hasColumn('employees', 'place_of_birth')) {
                $table->string('place_of_birth')->after('npwp')->nullable();
            }
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->string('gender')->after('birth_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'religion')) {
                $table->string('religion')->after('gender')->nullable();
            }
            if (!Schema::hasColumn('employees', 'marital_status')) {
                $table->string('marital_status')->after('religion')->nullable();
            }
            if (!Schema::hasColumn('employees', 'employee_status')) {
                $table->string('employee_status')->after('status')->nullable();
            }
            if (!Schema::hasColumn('employees', 'foundation_id')) {
                $table->unsignedBigInteger('foundation_id')->nullable()->after('employee_status');
            }
            if (!Schema::hasColumn('employees', 'education_level_id')) {
                $table->unsignedBigInteger('education_level_id')->nullable()->after('foundation_id');
            }
        });

        // positions (Job_Positions) table sync
        $tableName = Schema::hasTable('job_positions') ? 'job_positions' : (Schema::hasTable('positions') ? 'positions' : null);
        if ($tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    // Try to add after position_name if it exists, otherwise just add it
                    if (Schema::hasColumn($tableName, 'position_name')) {
                        $table->string('title')->after('position_name')->nullable();
                    } else {
                        $table->string('title')->nullable();
                    }
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    if (Schema::hasColumn($tableName, 'title')) {
                        $table->text('description')->after('title')->nullable();
                    } else {
                        $table->text('description')->nullable();
                    }
                }
            });
        }

        // employee_positions table sync
        Schema::table('employee_positions', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_positions', 'sk_file_name')) {
                $table->text('sk_file_name')->after('end_date')->nullable();
            }
            if (!Schema::hasColumn('employee_positions', 'sk_number')) {
                $table->string('sk_number')->after('sk_file_name')->nullable();
            }
            if (!Schema::hasColumn('employee_positions', 'base_on_salary')) {
                $table->integer('base_on_salary')->after('sk_number')->nullable();
            }
            if (!Schema::hasColumn('employee_positions', 'is_supervisor')) {
                $table->boolean('is_supervisor')->default(false)->after('base_on_salary');
            }
            if (!Schema::hasColumn('employee_positions', 'pay_grade_id')) {
                $table->unsignedBigInteger('pay_grade_id')->after('is_supervisor')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'emp_code', 'nik', 'npwp', 'place_of_birth', 
                'gender', 'religion', 'marital_status', 
                'employee_status', 'foundation_id', 'education_level_id'
            ]);
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('employee_positions', function (Blueprint $table) {
            $table->dropColumn([
                'sk_file_name', 'sk_number', 'base_on_salary', 
                'is_supervisor', 'pay_grade_id'
            ]);
        });
    }
};
