<?php

namespace Modules\Kpi\Database\Seeders;

use Illuminate\Database\Seeder;

class KpiDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed categories first (base configuration)
        $this->call(KpiCategorySeeder::class);

        // 2. Seed indicators (depend on categories)
        $this->call(KpiIndicatorSeeder::class);

        // 3. Seed daily tracking (attendance data for last 30 days)
        $this->call(KpiDailyTrackingSeeder::class);

        // 4. Seed tasks (assigned tasks for employees)
        $this->call(KpiTaskSeeder::class);

        // 5. Seed monthly reviews (behavior/bonus/penalty - depends on employees)
        $this->call(KpiMonthlyReviewSeeder::class);

        // 6. Seed monthly scores (final calculated scores for last 3 months)
        $this->call(KpiMonthlyScoreSeeder::class);
    }
}
