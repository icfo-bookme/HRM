<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'full_name', 'first_name', 'last_name', 'display_name',
                'phone', 'phone_2', 'profile_photo', 'signature_file',
                'gender', 'date_of_birth', 'nationality',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        // No rollback needed as these columns should never have been in employees table
    }
};