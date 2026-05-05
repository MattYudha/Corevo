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
        Schema::table('employee_kpi_records', function (Blueprint $table) {
            $table->string('submission_status')->default('draft')->after('status');
            $table->timestamp('submitted_at')->nullable()->after('submission_status');
            $table->foreignId('reviewed_by')->nullable()->after('submitted_at')->constrained('employees')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('reviewer_notes')->nullable()->after('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_kpi_records', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'submission_status',
                'submitted_at',
                'reviewed_by',
                'reviewed_at',
                'reviewer_notes'
            ]);
        });
    }
};
