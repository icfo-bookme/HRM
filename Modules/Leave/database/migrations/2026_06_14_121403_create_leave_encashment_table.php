<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = "CREATE TABLE `leave_encashment` (
            `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `employee_id`     BIGINT UNSIGNED NOT NULL,
            `leave_type_id`   BIGINT UNSIGNED NOT NULL,
            `encashment_date` DATE NOT NULL,
            `days_encashed`   DECIMAL(5,1) NOT NULL,
            `amount_per_day`  DECIMAL(14,2) DEFAULT NULL,
            `total_amount`    DECIMAL(14,2) DEFAULT NULL,
            `payroll_run_id`  BIGINT UNSIGNED DEFAULT NULL,
            `reason`          TEXT DEFAULT NULL,
            `approved_by`     BIGINT UNSIGNED DEFAULT NULL,
            `approved_at`     DATETIME DEFAULT NULL,
            `status`          ENUM('Pending','Approved','Paid') DEFAULT 'Pending',
            `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT `fk_encash_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_encash_leavetype` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_encash_approvedby` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
            INDEX `idx_encashment_employee` (`employee_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave encashment requests'";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_encashment', function (Blueprint $table) {
            $table->dropForeign('fk_encash_employee');
            $table->dropForeign('fk_encash_leavetype');
            $table->dropForeign('fk_encash_approvedby');
        });

        Schema::dropIfExists('leave_encashment');
    }
};