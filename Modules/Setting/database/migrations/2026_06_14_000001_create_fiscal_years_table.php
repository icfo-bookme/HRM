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
        $sql = "CREATE TABLE `fiscal_years` (
            `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `company_id` BIGINT UNSIGNED NOT NULL,
            `label`      VARCHAR(20) NOT NULL,
            `start_date` DATE NOT NULL,
            `end_date`   DATE NOT NULL,
            `is_current` TINYINT(1) DEFAULT 0,
            `locked`     TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_fy_company_label` (`company_id`, `label`),
            CONSTRAINT `fk_fy_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE RESTRICT,
            INDEX `idx_fy_current` (`company_id`, `is_current`),
            INDEX `idx_fy_dates` (`start_date`, `end_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Financial year definitions'";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->dropForeign('fk_fy_company');
        });

        Schema::dropIfExists('fiscal_years');
    }
};