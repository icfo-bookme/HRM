<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = "CREATE TABLE `employee_leave_balance` (
            `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `employee_id`     BIGINT UNSIGNED NOT NULL,
            `leave_type_id`   BIGINT UNSIGNED NOT NULL,
            `fiscal_year_id`  BIGINT UNSIGNED NOT NULL,
            `opening_balance` DECIMAL(5,1) DEFAULT 0,
            `earned_days`     DECIMAL(5,1) DEFAULT 0,
            `used_days`       DECIMAL(5,1) DEFAULT 0,
            `encashed_days`   DECIMAL(5,1) DEFAULT 0,
            `lapsed_days`     DECIMAL(5,1) DEFAULT 0,
            `pending_days`    DECIMAL(5,1) DEFAULT 0,
            `remaining_days`  DECIMAL(5,1) GENERATED ALWAYS AS (
                opening_balance + earned_days - used_days - encashed_days - lapsed_days - pending_days
            ) STORED,
            `updated_at`      TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_leave_balance` (`employee_id`, `leave_type_id`, `fiscal_year_id`),
            INDEX `idx_balance_employee` (`employee_id`),
            INDEX `idx_balance_leavetype` (`leave_type_id`),
            INDEX `idx_balance_fiscal` (`fiscal_year_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave balance per employee'";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balance');
    }
};