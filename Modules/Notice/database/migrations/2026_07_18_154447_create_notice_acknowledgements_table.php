<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice_acknowledgements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notice_id');
            $table->unsignedBigInteger('employee_id');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Each employee can acknowledge only once per notice
            $table->unique(['notice_id', 'employee_id']);

            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_acknowledgements');
    }
};