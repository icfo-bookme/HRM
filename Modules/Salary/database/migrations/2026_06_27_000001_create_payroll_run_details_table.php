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
        Schema::create('payroll_run_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')
                ->constrained('payroll_runs')
                ->cascadeOnDelete()
                ->comment('FK to the locked payroll run');
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();
            $table->string('employee_name', 255)->nullable()
                ->comment('Snapshot of employee name at lock time');
            $table->string('employee_code', 100)->nullable()
                ->comment('Snapshot of employee code at lock time');
            $table->decimal('basic_salary', 14, 4)->default(0)
                ->comment('Basic salary used for this run');
            $table->decimal('gross', 14, 4)->default(0)
                ->comment('Total earnings before deductions');
            $table->decimal('deductions', 14, 4)->default(0)
                ->comment('Total deductions');
            $table->decimal('net', 14, 4)->default(0)
                ->comment('Net pay = gross - deductions');
            $table->json('component_details')
                ->comment('Snapshot of all earning & deduction components with calculation breakdown');
            $table->json('attendance_summary')
                ->nullable()
                ->comment('Attendance adjustment data (present, late, overtime, half-day, absent)');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            // One detail record per employee per payroll run
            $table->unique(['payroll_run_id', 'employee_id'], 'uk_payroll_run_employee');

            $table->index('payroll_run_id', 'idx_detail_payroll_run');
            $table->index('employee_id', 'idx_detail_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_run_details');
    }
};
