<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_dependents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('full_name', 200);
            $table->string('relation', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('occupation', 200)->nullable();
            $table->boolean('is_nominee')->default(false);
            $table->decimal('nominee_percent', 5, 2)->nullable();
            $table->integer('priority_order')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_dependents');
    }
};