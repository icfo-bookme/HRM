<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing loan_number column
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'loan_number')) {
                $table->string('loan_number', 20)->after('id')->comment('Professional tracking ID: LN-YYYY-XXXX');
            }
        });

        // Now add unique index to loan_number (only if column exists and index doesn't)
        if (Schema::hasColumn('loans', 'loan_number')) {
            try {
                Schema::table('loans', function (Blueprint $table) {
                    $table->unique('loan_number', 'loans_loan_number_unique');
                });
            } catch (\Exception $e) {
                // Index may already exist, ignore
            }
        }

        // Add missing indexes that exist in the pending migration
        try {
            Schema::table('loans', function (Blueprint $table) {
                $table->index('loan_type', 'idx_loans_type');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        try {
            Schema::table('loans', function (Blueprint $table) {
                $table->index('application_date', 'idx_loans_application_date');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }

        // Add missing columns to loan_installments (notes field)
        Schema::table('loan_installments', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_installments', 'notes')) {
                $table->text('notes')->nullable()->after('paid_at');
            }
        });

        // Add unique constraint to loan_installments
        try {
            Schema::table('loan_installments', function (Blueprint $table) {
                $table->unique(['loan_id', 'installment_no'], 'uq_installment_no');
            });
        } catch (\Exception $e) {
            // May already exist
        }
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'loan_number')) {
                $table->dropColumn('loan_number');
            }
        });
    }
};