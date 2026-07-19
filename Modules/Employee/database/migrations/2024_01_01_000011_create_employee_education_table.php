<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('degree', 200);
            $table->string('major_subject', 200)->nullable();
            $table->string('institution', 300)->nullable();
            $table->string('board_university', 300)->nullable();
            $table->year('passing_year')->nullable();
            $table->enum('result_type', ['CGPA', 'Percentage', 'Grade', 'Division'])->nullable();
            $table->string('result_value', 50)->nullable();
            $table->date('duration_from')->nullable();
            $table->date('duration_to')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->boolean('is_highest')->default(false);
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};