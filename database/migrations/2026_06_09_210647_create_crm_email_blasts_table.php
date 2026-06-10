<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('crm_email_blasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');
            $table->enum('status', ['draft', 'processing', 'completed'])->default('draft');
            $table->integer('target_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_blasts');
    }
};
