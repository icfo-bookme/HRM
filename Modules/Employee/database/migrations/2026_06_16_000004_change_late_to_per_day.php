<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendance_rules', function (Blueprint $table) {
            $table->renameColumn('late_deduction_per_minute', 'late_deduction_per_day');
            $table->decimal('late_deduction_per_day', 10, 2)->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendance_rules', function (Blueprint $table) {
            $table->renameColumn('late_deduction_per_day', 'late_deduction_per_minute');
            $table->decimal('late_deduction_per_minute', 10, 4)->default(0)->change();
        });
    }
};