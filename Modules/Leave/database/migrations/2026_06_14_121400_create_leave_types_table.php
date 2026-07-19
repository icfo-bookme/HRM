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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('days_per_year', 5, 1)->default(0);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_half_day_allowed')->default(true);
            $table->boolean('carry_forward')->default(false);
            $table->decimal('max_carry_days', 5, 1)->default(0);
            $table->integer('max_consecutive_days')->default(0);
            $table->boolean('requires_document')->default(false);
            $table->integer('min_days_notice')->default(0);
            $table->enum('applicable_gender', ['All', 'Male', 'Female'])->default('All');
            $table->string('color_code', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active', 'idx_leavetype_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};