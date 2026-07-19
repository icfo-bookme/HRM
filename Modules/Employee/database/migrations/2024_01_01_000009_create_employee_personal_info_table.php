<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_personal_info', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->primary();
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('full_name', 300)->nullable();
            $table->string('display_name', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone_2', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('profile_photo', 500)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->default('Bangladeshi');
            $table->string('personal_email', 200)->nullable();
            $table->string('personal_mobile', 20)->nullable();
            $table->string('place_of_birth', 200)->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('religion', 80)->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'])->nullable();
            $table->string('spouse_name', 200)->nullable();
            $table->string('father_name', 200)->nullable();
            $table->string('mother_name', 200)->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->date('nid_issue_date')->nullable();
            $table->date('nid_expiry_date')->nullable();
            $table->string('nid_file_path', 500)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('passport_file_path', 500)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->string('tin_file_path', 500)->nullable();
            $table->string('birth_certificate', 50)->nullable();
            $table->string('driving_license', 50)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('nid_number');
            $table->index('passport_number');
            $table->index('tin_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_personal_info');
    }
};