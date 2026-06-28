<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_run_details', function (Blueprint $table) {
            $table->tinyInteger('payment_status')
                ->default(0)
                ->comment('0 = Unpaid, 1 = Paid')
                ->after('attendance_summary');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_run_details', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
