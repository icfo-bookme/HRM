<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_monthly_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('reviewer_id');          // manager
            $table->integer('year');
            $table->integer('month');

            // Behavior (0-10) - Optional
            $table->boolean('give_behavior')->default(false);   // ★ দিবে কিনা?
            $table->decimal('behavior_score', 4, 1)->nullable(); // 0-10
            $table->text('behavior_remarks')->nullable();

            // Bonus (0-10) - Optional
            $table->boolean('give_bonus')->default(false);      // ★ দিবে কিনা?
            $table->decimal('bonus_score', 4, 1)->nullable();    // 0-10
            $table->text('bonus_remarks')->nullable();

            // Penalty (0-10) - Optional
            $table->boolean('give_penalty')->default(false);    // ★ দিবে কিনা?
            $table->decimal('penalty_score', 4, 1)->nullable();  // 0-10
            $table->text('penalty_remarks')->nullable();

            $table->enum('status', ['Draft', 'Submitted', 'Approved'])->default('Draft');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('employees')->onDelete('restrict');
            $table->unique(['employee_id', 'year', 'month']);
            $table->index(['reviewer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_monthly_reviews');
    }
};
