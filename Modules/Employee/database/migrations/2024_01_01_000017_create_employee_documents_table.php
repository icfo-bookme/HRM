<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('document_name', 300)->nullable();
            $table->string('file_path', 500);
            $table->string('file_hash', 64)->nullable()->comment('SHA-256 for deduplication');
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_number', 100)->nullable();
            $table->string('issuing_authority', 300)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('document_categories')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
            $table->index('employee_id');
            $table->index('expiry_date');
            $table->index('category_id');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};