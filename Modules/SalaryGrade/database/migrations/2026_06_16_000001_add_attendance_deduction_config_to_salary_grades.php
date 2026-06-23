<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_grades', function (Blueprint $table) {
            $table->boolean('deduct_late_for_payroll')->default(true)->after('is_active');
            $table->boolean('pay_overtime_for_payroll')->default(true)->after('deduct_late_for_payroll');
            $table->decimal('late_deduction_per_minute', 10, 4)->default(0)->after('pay_overtime_for_payroll');
            $table->decimal('half_day_deduction_percent', 5, 2)->default(50)->after('late_deduction_per_minute');
            $table->decimal('absent_deduction_days', 5, 2)->default(1)->after('half_day_deduction_percent');
        });
    }

    public function down(): void
    {
        Schema::table('salary_grades', function (Blueprint $table) {
            $table->dropColumn([
                'deduct_late_for_payroll',
                'pay_overtime_for_payroll',
                'late_deduction_per_minute',
                'half_day_deduction_percent',
                'absent_deduction_days',
            ]);
        });
    }
};