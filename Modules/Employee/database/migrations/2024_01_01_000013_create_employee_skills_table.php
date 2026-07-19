<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('skill_name', 200);
            $table->text('description')->nullable();
            $table->enum('proficiency', ['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'])->default('Intermediate');
            $table->decimal('years_of_experience', 3, 1)->nullable();
            $table->date('last_used_date')->nullable();
            $table->string('certification', 300)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['employee_id', 'skill_name']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('skill_categories')->onDelete('set null');
            $table->index('employee_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_skills');
    }
};