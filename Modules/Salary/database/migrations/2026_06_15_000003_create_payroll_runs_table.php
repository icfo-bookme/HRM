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
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
            $table->date('run_month')->comment('First day of month');
            $table->string('run_label', 100)->nullable();
            $table->enum('run_type', ['Regular', 'Bonus', 'Advance', 'Adjustment'])->default('Regular');
            $table->integer('total_employees')->default(0);
            $table->decimal('total_gross', 16, 2)->default(0);
            $table->decimal('total_net', 16, 2)->default(0);
            $table->decimal('total_deductions', 16, 2)->default(0);
            $table->enum('status', ['Draft', 'Processing', 'Calculated', 'Reviewed', 'Approved', 'Disbursed', 'Locked', 'Cancelled'])->default('Draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users');
            $table->dateTime('disbursed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['run_month', 'run_type'], 'uk_payroll_run');
            $table->index('status', 'idx_payroll_status');
            $table->index('run_month', 'idx_payroll_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};