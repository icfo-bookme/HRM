<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendance_rules', function (Blueprint $table) {
            // Drop old late_deduction_per_day column
            $table->dropColumn('late_deduction_per_day');
            
            // Add new columns for the 3 types
            $table->decimal('late_deduction_per_minute', 10, 4)->default(0)->after('late_grace_minutes');
            $table->decimal('late_deduction_fixed', 10, 2)->default(0)->after('late_deduction_per_minute');
        });

        // Change the enum for late_deduction_type
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE employee_attendance_rules MODIFY COLUMN late_deduction_type ENUM('none','per_minute','half_day','full_day') DEFAULT 'per_minute'");
    }

    public function down(): void
    {
        Schema::table('employee_attendance_rules', function (Blueprint $table) {
            $table->dropColumn(['late_deduction_per_minute', 'late_deduction_fixed']);
        });
    }
};