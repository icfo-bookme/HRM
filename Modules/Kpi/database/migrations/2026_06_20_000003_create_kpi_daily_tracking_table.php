<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_daily_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('tracking_date');

            // Working day check
            $table->boolean('is_working_day')->default(false);

            // Present tracking
            $table->boolean('is_present')->default(false);
            $table->boolean('is_late')->default(false);
            $table->decimal('present_target', 5, 1)->default(0);   // if working_day → +1
            $table->decimal('present_obtained', 5, 1)->default(0);  // if present → +1

            // Late tracking
            $table->decimal('late_target', 5, 1)->default(0);      // if working_day → +1
            $table->decimal('late_obtained', 5, 1)->default(0);     // if late → -2

            // Daily totals
            $table->decimal('daily_target', 8, 2)->default(0);
            $table->decimal('daily_obtained', 8, 2)->default(0);
            $table->decimal('daily_percentage', 5, 2)->default(0);

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'tracking_date']);
            $table->index(['employee_id', 'tracking_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_daily_tracking');
    }
};
