<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->string('notice_no', 50)->unique()->nullable();
            $table->string('title', 255);
            $table->string('slug', 255)->nullable();
            $table->longText('description');

            $table->enum('notice_type', [
                'General', 'HR', 'Holiday', 'Attendance',
                'Payroll', 'Policy', 'Training', 'Event', 'Emergency'
            ])->default('General');

            $table->enum('priority', [
                'Low', 'Medium', 'High', 'Urgent'
            ])->default('Medium');

            $table->dateTime('publish_date');
            $table->dateTime('expiry_date')->nullable();

            $table->enum('target_type', [
                'All', 'Department', 'Designation', 'Branch', 'Employee'
            ])->default('All');

            $table->string('attachment_path', 500)->nullable();

            $table->boolean('is_popup')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->foreign('branch_id')->references('id')->on('branches');

            $table->index('notice_type', 'idx_notice_type');
            $table->index('priority', 'idx_priority');
            $table->index('publish_date', 'idx_publish_date');
            $table->index('expiry_date', 'idx_expiry_date');
            $table->index('is_active', 'idx_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};