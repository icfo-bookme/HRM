<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('code', 30);
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_employee_id')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['branch_id', 'code']);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('parent_id')->references('id')->on('departments')->onDelete('set null');
            $table->index('branch_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};