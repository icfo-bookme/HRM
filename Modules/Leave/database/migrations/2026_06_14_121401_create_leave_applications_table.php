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
        $sql = "CREATE TABLE `leave_applications` (
            `id`                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `employee_id`             BIGINT UNSIGNED NOT NULL,
            `leave_type_id`           BIGINT UNSIGNED NOT NULL,
            `application_no`          VARCHAR(30) UNIQUE,
            `from_date`               DATE NOT NULL,
            `to_date`                 DATE NOT NULL,
            `total_days`              DECIMAL(5,1) NOT NULL,
            `is_half_day`             TINYINT(1) DEFAULT 0,
            `half_day_period`         ENUM('First Half','Second Half') DEFAULT NULL,
            `reason`                  TEXT DEFAULT NULL,
            `document_path`           VARCHAR(500) DEFAULT NULL,
            `substitute_employee_id`  BIGINT UNSIGNED DEFAULT NULL,
            `contact_during_leave`    VARCHAR(50) DEFAULT NULL,
            `status`                  ENUM('Draft','Pending','Approved','Rejected','Cancelled','Withdrawn') DEFAULT 'Pending',
            `rejection_reason`        TEXT DEFAULT NULL,
            `approved_by`             BIGINT UNSIGNED DEFAULT NULL,
            `approved_at`             DATETIME DEFAULT NULL,
            `applied_at`              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at`              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_leaveapp_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_leaveapp_leavetype` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_leaveapp_approvedby` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
            CONSTRAINT `fk_leaveapp_substitute` FOREIGN KEY (`substitute_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
            INDEX `idx_leave_employee` (`employee_id`),
            INDEX `idx_leave_dates` (`from_date`, `to_date`),
            INDEX `idx_leave_status` (`status`),
            INDEX `idx_leave_number` (`application_no`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave request submissions'";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropForeign('fk_leaveapp_employee');
            $table->dropForeign('fk_leaveapp_leavetype');
            $table->dropForeign('fk_leaveapp_approvedby');
            $table->dropForeign('fk_leaveapp_substitute');
        });

        Schema::dropIfExists('leave_applications');
    }
};