<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->date('roster_date');
            $table->boolean('is_day_off')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('set null');
            $table->index('employee_id');
            $table->index('roster_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_rosters');
    }
};