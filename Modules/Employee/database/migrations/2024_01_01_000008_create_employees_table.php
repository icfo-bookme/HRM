<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 50)->unique();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->unsignedBigInteger('reports_to')->nullable();
            $table->enum('employment_type', ['Full-Time', 'Part-Time', 'Contractual', 'Intern', 'Probation', 'Freelance'])->default('Full-Time');
            $table->date('joining_date');
            $table->date('confirmation_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->date('last_working_day')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'On Leave', 'Suspended', 'Terminated', 'Resigned', 'Retired'])->default('Active');
            $table->boolean('portal_active')->default(false);
            $table->datetime('portal_last_login')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('restrict');
            $table->foreign('grade_id')->references('id')->on('salary_grades')->onDelete('set null');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('reports_to')->references('id')->on('employees')->onDelete('set null');
            $table->index('status');
            $table->index(['department_id', 'status']);
            $table->index('joining_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};