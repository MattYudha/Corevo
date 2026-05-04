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
        Schema::create('positions', function (Blueprint $table) {
            $table->id('position_id');
            $table->string('position_name');
            $table->string('title')->nullable();
            $table->string('level')->nullable();
            $table->string('salary_grade')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions', 'position_id')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('sk_file_name')->nullable();
            $table->string('sk_number')->nullable();
            $table->decimal('base_on_salary', 15, 2)->nullable();
            $table->boolean('is_supervisor')->default(false);
            $table->unsignedBigInteger('pay_grade_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_positions');
        Schema::dropIfExists('positions');
    }
};
