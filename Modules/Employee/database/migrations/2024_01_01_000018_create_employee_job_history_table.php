<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_job_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('effective_date');
            $table->enum('change_type', [
                'Joining', 'Promotion', 'Demotion', 'Transfer', 'Designation Change',
                'Grade Change', 'Salary Revision', 'Confirmation', 'Termination',
                'Resignation', 'Retirement', 'Rehired'
            ]);
            $table->unsignedBigInteger('from_branch_id')->nullable();
            $table->unsignedBigInteger('to_branch_id')->nullable();
            $table->unsignedBigInteger('from_dept_id')->nullable();
            $table->unsignedBigInteger('to_dept_id')->nullable();
            $table->unsignedBigInteger('from_desig_id')->nullable();
            $table->unsignedBigInteger('to_desig_id')->nullable();
            $table->unsignedBigInteger('from_grade_id')->nullable();
            $table->unsignedBigInteger('to_grade_id')->nullable();
            $table->decimal('from_salary', 14, 2)->nullable();
            $table->decimal('to_salary', 14, 2)->nullable();
            $table->text('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('document_ref', 500)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('from_dept_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('to_dept_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('from_desig_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('to_desig_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('from_grade_id')->references('id')->on('salary_grades')->onDelete('set null');
            $table->foreign('to_grade_id')->references('id')->on('salary_grades')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
            $table->index(['employee_id', 'effective_date']);
            $table->index('effective_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_job_history');
    }
};