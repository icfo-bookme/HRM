<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('shift_id')->nullable();

            $table->date('attendance_date');

            $table->dateTime('first_in_at')->nullable();
            $table->dateTime('last_out_at')->nullable();

            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();

            $table->unsignedInteger('break_minutes')->default(0);

            $table->unsignedInteger('working_minutes')->default(0);
            $table->unsignedInteger('net_working_minutes')->default(0);

            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_out_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);

            $table->boolean('is_late')->default(false);
            $table->boolean('is_early_out')->default(false);
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_holiday_work')->default(false);

            $table->enum('attendance_status', [
                'Present',
                'Absent',
                'Half Day',
                'On Leave',
                'Holiday',
                'Weekend'
            ])->default('Present');

            $table->enum('approval_status', [
                'Pending',
                'Approved',
                'Rejected'
            ])->default('Approved');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();

            $table->text('remarks')->nullable();

            $table->enum('source', [
                'Device',
                'Manual',
                'CSV',
                'API',
                'Mobile App',
                'Web'
            ])->default('Device');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('shift_id')->references('id')->on('shifts');

            $table->unique(['employee_id', 'attendance_date']);

            $table->index(['employee_id']);
            $table->index(['attendance_date']);
            $table->index(['attendance_status']);
            $table->index(['approval_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};