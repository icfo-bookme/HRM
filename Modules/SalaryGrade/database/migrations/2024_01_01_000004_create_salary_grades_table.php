<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('min_salary', 14, 2)->default(0);
            $table->decimal('max_salary', 14, 2)->default(0);
            $table->char('currency', 3)->default('BDT');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_grades');
    }
};