<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->unique();

            // Overtime settings
            $table->boolean('enable_overtime')->default(true);
            $table->decimal('overtime_rate_per_hour', 10, 2)->default(0);
            $table->decimal('overtime_multiplier', 4, 2)->default(1.50);

            // Late settings
            $table->boolean('enable_late_deduction')->default(true);
            $table->enum('late_deduction_type', ['none', 'per_minute', 'half_day', 'full_day'])->default('per_minute');
            $table->decimal('late_deduction_per_minute', 10, 4)->default(0);
            $table->integer('late_grace_minutes')->default(0);

            // Half day settings
            $table->boolean('enable_half_day_deduction')->default(true);
            $table->decimal('half_day_deduction_percent', 5, 2)->default(50);

            // Absent settings
            $table->boolean('enable_absent_deduction')->default(true);
            $table->decimal('absent_deduction_days', 5, 2)->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance_rules');
    }
};