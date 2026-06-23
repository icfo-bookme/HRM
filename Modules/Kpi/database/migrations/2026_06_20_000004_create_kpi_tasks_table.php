<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('assigned_by');          // manager
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_score', 8, 2);              // ★ Assign করলেই target এ যোগ হবে
            $table->decimal('obtained_score', 8, 2)->nullable(); // ★ Complete করলে obtain এ যোগ হবে
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->date('assigned_date');
            $table->date('deadline')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled', 'Overdue'])->default('Pending');
            $table->dateTime('completed_at')->nullable();
            $table->text('completion_note')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('employees')->onDelete('restrict');
            $table->index(['employee_id', 'status']);
            $table->index('assigned_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_tasks');
    }
};
