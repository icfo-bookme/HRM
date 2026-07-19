<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_experience', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('company_name', 300);
            $table->string('designation', 200)->nullable();
            $table->string('department', 200)->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('responsibilities')->nullable();
            $table->text('achievements')->nullable();
            $table->string('reason_for_leaving', 300)->nullable();
            $table->string('salary_scale', 100)->nullable();
            $table->string('reference_name', 200)->nullable();
            $table->string('reference_phone', 20)->nullable();
            $table->string('reference_email', 200)->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
            $table->index(['from_date', 'to_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_experience');
    }
};