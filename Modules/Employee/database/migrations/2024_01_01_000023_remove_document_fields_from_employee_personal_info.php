<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_personal_info', function (Blueprint $table) {
            $columns = [
                'nid_number', 'nid_issue_date', 'nid_expiry_date', 'nid_file_path',
                'passport_number', 'passport_issue_date', 'passport_expiry', 'passport_file_path',
                'tin_number', 'tin_file_path',
                'birth_certificate', 'driving_license',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_personal_info', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_personal_info', function (Blueprint $table) {
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
            $table->index('nid_number');
            $table->index('passport_number');
            $table->index('tin_number');
        });
    }
};