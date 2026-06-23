<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_monthly_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('year');
            $table->integer('month');

            // === ATTENDANCE (Always Count - Auto) ===
            $table->integer('working_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->decimal('attendance_target', 8, 2)->default(0);
            $table->decimal('attendance_obtained', 8, 2)->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);

            // === TASK (Always Count) ===
            $table->integer('total_assigned_tasks')->default(0);
            $table->integer('completed_tasks')->default(0);
            $table->decimal('task_target', 8, 2)->default(0);
            $table->decimal('task_obtained', 8, 2)->default(0);
            $table->decimal('task_percentage', 5, 2)->default(0);

            // === BEHAVIOR (Optional - only if manager gives) ===
            $table->boolean('behavior_given')->default(false);
            $table->decimal('behavior_target', 8, 2)->default(0);
            $table->decimal('behavior_obtained', 8, 2)->default(0);
            $table->decimal('behavior_percentage', 5, 2)->default(0);

            // === BONUS (Optional - only if manager gives) ===
            $table->boolean('bonus_given')->default(false);
            $table->decimal('bonus_target', 8, 2)->default(0);
            $table->decimal('bonus_obtained', 8, 2)->default(0);
            $table->decimal('bonus_percentage', 5, 2)->default(0);

            // === PENALTY (Optional - only if manager gives) ===
            $table->boolean('penalty_given')->default(false);
            $table->decimal('penalty_target', 8, 2)->default(0);
            $table->decimal('penalty_obtained', 8, 2)->default(0);
            $table->decimal('penalty_percentage', 5, 2)->default(0);

            // === OVERALL ===
            $table->decimal('total_target', 8, 2)->default(0);
            $table->decimal('total_obtained', 8, 2)->default(0);
            $table->decimal('overall_percentage', 5, 2)->default(0);
            $table->enum('rating', ['A+', 'A', 'B+', 'B', 'C', 'D'])->nullable();

            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'year', 'month']);
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_monthly_scores');
    }
};
