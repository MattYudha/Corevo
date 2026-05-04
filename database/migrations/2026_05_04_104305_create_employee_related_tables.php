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
        Schema::create('education_levels', function (Blueprint $table) {
            $table->id('education_level_id');
            $table->string('level');
            $table->timestamps();
        });

        Schema::create('identity_types', function (Blueprint $table) {
            $table->id('identity_type_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('employee_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('fullname');
            $table->string('relation')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('account_holder')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('document_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('identity_type_id')->nullable()->constrained('identity_types', 'identity_type_id')->nullOnDelete();
            $table->string('identity_number')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert some default education levels and identity types
        DB::table('education_levels')->insert([
            ['level' => 'SD'],
            ['level' => 'SMP'],
            ['level' => 'SMA/SMK'],
            ['level' => 'D3'],
            ['level' => 'S1'],
            ['level' => 'S2'],
            ['level' => 'S3'],
        ]);

        DB::table('identity_types')->insert([
            ['name' => 'KTP'],
            ['name' => 'SIM'],
            ['name' => 'Passport'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_identities');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('employee_families');
        Schema::dropIfExists('identity_types');
        Schema::dropIfExists('education_levels');
    }
};
