<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->string('code', 30)->nullable();
            $table->string('title', 200);
            $table->tinyInteger('level')->default(1);
            $table->json('responsibilities')->nullable();
            $table->json('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('grade_id')->references('id')->on('salary_grades')->onDelete('set null');
            $table->index('department_id');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};