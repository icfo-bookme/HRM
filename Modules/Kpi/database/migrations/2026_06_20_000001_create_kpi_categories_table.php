<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Attendance, Task, Behavior, Bonus, Penalty
            $table->string('name_bn')->nullable();   // Bengali name (optional)
            $table->decimal('weight_percentage', 5, 2); // e.g. Attendance=20%, Task=30%
            $table->enum('calculation_type', ['Daily Auto', 'Per Task', 'Monthly Optional']);
            $table->enum('point_setting', ['System Defined', 'Manager Assign', 'Manager Input']);
            $table->integer('sort_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_categories');
    }
};
