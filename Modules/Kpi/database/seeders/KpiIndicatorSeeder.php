<?php

namespace Modules\Kpi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KpiIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $attendanceId = DB::table('kpi_categories')->where('name', 'Attendance')->value('id');
        $taskId = DB::table('kpi_categories')->where('name', 'Task')->value('id');
        $behaviorId = DB::table('kpi_categories')->where('name', 'Behavior')->value('id');
        $bonusId = DB::table('kpi_categories')->where('name', 'Bonus')->value('id');
        $penaltyId = DB::table('kpi_categories')->where('name', 'Penalty')->value('id');

        $indicators = [
            [
                'category_id' => $attendanceId,
                'key' => 'present',
                'name' => 'Present',
                'name_bn' => null,
                'weight_percentage' => 50.00,
                'point_per_unit' => 1.00,
                'default_max_score' => null,
                'count_behavior' => 'Always Count',
                'is_active' => true,
            ],
            [
                'category_id' => $attendanceId,
                'key' => 'late',
                'name' => 'Late',
                'name_bn' => null,
                'weight_percentage' => 50.00,
                'point_per_unit' => -2.00,
                'default_max_score' => null,
                'count_behavior' => 'Always Count',
                'is_active' => true,
            ],
            [
                'category_id' => $taskId,
                'key' => 'task',
                'name' => 'Task',
                'name_bn' => null,
                'weight_percentage' => 100.00,
                'point_per_unit' => null,
                'default_max_score' => null,
                'count_behavior' => 'Always Count',
                'is_active' => true,
            ],
            [
                'category_id' => $behaviorId,
                'key' => 'behavior',
                'name' => 'Behavior',
                'name_bn' => null,
                'weight_percentage' => 100.00,
                'point_per_unit' => null,
                'default_max_score' => 10.00,
                'count_behavior' => 'Optional Count',
                'is_active' => true,
            ],
            [
                'category_id' => $bonusId,
                'key' => 'bonus',
                'name' => 'Bonus',
                'name_bn' => null,
                'weight_percentage' => 100.00,
                'point_per_unit' => null,
                'default_max_score' => 10.00,
                'count_behavior' => 'Optional Count',
                'is_active' => true,
            ],
            [
                'category_id' => $penaltyId,
                'key' => 'penalty',
                'name' => 'Penalty',
                'name_bn' => null,
                'weight_percentage' => 100.00,
                'point_per_unit' => null,
                'default_max_score' => 10.00,
                'count_behavior' => 'Optional Count',
                'is_active' => true,
            ],
        ];

        foreach ($indicators as $indicator) {
            \Modules\Kpi\Models\KpiIndicator::updateOrCreate(
                ['category_id' => $indicator['category_id'], 'key' => $indicator['key']],
                $indicator
            );
        }

        $this->command->info('✓ KPI indicators seeded successfully!');
    }
}
