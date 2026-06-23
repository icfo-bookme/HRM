<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_banking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('bank_name', 200)->nullable();
            $table->string('bank_branch', 200)->nullable();
            $table->string('bank_account', 80)->nullable();
            $table->string('bank_routing', 50)->nullable();
            $table->string('iban', 50)->nullable();
            $table->string('swift_code', 20)->nullable();
            $table->enum('mfs_type', ['bKash', 'Nagad', 'Rocket', 'Upay', 'Others'])->nullable();
            $table->string('mfs_number', 20)->nullable();
            $table->enum('payment_method', ['Bank', 'Cash', 'MFS', 'Cheque'])->default('Bank');
            $table->boolean('is_primary')->default(true);
            $table->enum('verification_status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->datetime('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');
            $table->unique(['employee_id', 'is_primary']);
            $table->index('bank_account');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_banking');
    }
};