<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE loan_installments MODIFY status ENUM('Pending', 'Progress', 'Paid', 'Partial', 'Overdue', 'Waived') DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::table('loan_installments')
            ->where('status', 'Progress')
            ->update(['status' => 'Pending']);

        DB::statement("ALTER TABLE loan_installments MODIFY status ENUM('Pending', 'Paid', 'Partial', 'Overdue', 'Waived') DEFAULT 'Pending'");
    }
};
