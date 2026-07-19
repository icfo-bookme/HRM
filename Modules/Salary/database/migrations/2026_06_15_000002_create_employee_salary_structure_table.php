<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_salary_structure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('salary_components')->cascadeOnDelete();
            $table->decimal('amount', 14, 4)->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_percentage')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('employee_id', 'idx_salary_employee');
            $table->index(['effective_from', 'effective_to'], 'idx_salary_effective');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_structure');
    }
};