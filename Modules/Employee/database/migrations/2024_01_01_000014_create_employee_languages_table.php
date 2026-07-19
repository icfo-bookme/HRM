<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_languages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('language_name', 50);
            $table->enum('proficiency', ['Basic', 'Conversational', 'Professional', 'Native'])->default('Basic');
            $table->boolean('can_read')->default(false);
            $table->boolean('can_write')->default(false);
            $table->boolean('can_speak')->default(false);
            $table->timestamps();
            $table->unique(['employee_id', 'language_name']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_languages');
    }
};