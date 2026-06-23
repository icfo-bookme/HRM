<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('count_late_for_payroll')->default(true)->after('shift_id');
            $table->boolean('count_overtime_for_payroll')->default(true)->after('count_late_for_payroll');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['count_late_for_payroll', 'count_overtime_for_payroll']);
        });
    }
};