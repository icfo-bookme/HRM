<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('holiday_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamps();

            $table->index('holiday_id', 'idx_ha_holiday_id');
            $table->index('branch_id', 'idx_ha_branch_id');
            $table->index('department_id', 'idx_ha_department_id');
            $table->unique(['holiday_id', 'branch_id', 'department_id'], 'uk_holiday_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_assignments');
    }
};