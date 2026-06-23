<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_awards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('award_name', 200);
            $table->date('award_date')->nullable();
            $table->string('awarded_by', 200)->nullable();
            $table->string('organization', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_awards');
    }
};