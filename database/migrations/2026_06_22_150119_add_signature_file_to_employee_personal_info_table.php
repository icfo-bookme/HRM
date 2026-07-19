<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_personal_info', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_personal_info', 'signature_file')) {
                $table->string('signature_file', 500)->nullable()->after('profile_photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personal_info', function (Blueprint $table) {
            if (Schema::hasColumn('employee_personal_info', 'signature_file')) {
                $table->dropColumn('signature_file');
            }
        });
    }
};