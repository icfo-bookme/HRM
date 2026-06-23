<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number', 20)->unique()->comment('Professional tracking ID: LN-YYYY-XXXX');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('loan_type', ['Personal', 'Emergency', 'Education', 'Medical', 'Vehicle', 'Home', 'Other'])->default('Personal');
            
            // Financial Details
            $table->decimal('loan_amount', 14, 2)->default(0);
            $table->decimal('total_interest', 14, 2)->default(0);
            $table->decimal('total_payable', 14, 2)->default(0);
            $table->decimal('installment_amount', 14, 2)->default(0);
            $table->integer('total_installments')->default(1);
            $table->integer('paid_installments')->default(0);
            $table->decimal('remaining_amount', 14, 2)->default(0);
            
            // Purpose & Description
            $table->text('purpose')->nullable();
            
            // Lifecycle Dates
            $table->date('application_date');
            $table->date('approval_date')->nullable();
            $table->date('first_installment_date')->nullable();
            $table->date('disbursement_date')->nullable();
            
            // Status Lifecycle: Pending → Approved → Disbursed → Completed | Rejected | Cancelled
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Disbursed', 'Completed', 'Cancelled'])->default('Pending');
            
            // Approval Trail
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Creator
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Performance Indexes
            $table->index('employee_id', 'idx_loans_employee');
            $table->index('status', 'idx_loans_status');
            $table->index('loan_type', 'idx_loans_type');
            $table->index('application_date', 'idx_loans_application_date');
        });

        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->integer('installment_no');
            $table->date('due_date');
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->enum('status', ['Pending', 'Paid', 'Partial', 'Overdue', 'Waived'])->default('Pending');
            $table->foreignId('payroll_run_id')->nullable()->constrained('payroll_runs')->nullOnDelete();
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Performance Indexes
            $table->index('loan_id', 'idx_installments_loan');
            $table->index('status', 'idx_installments_status');
            $table->index('due_date', 'idx_installments_due');
            $table->unique(['loan_id', 'installment_no'], 'uq_installment_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
        Schema::dropIfExists('loans');
    }
};