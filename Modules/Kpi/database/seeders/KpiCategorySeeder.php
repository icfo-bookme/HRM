<?php

namespace Modules\Kpi\Database\Seeders;

use Illuminate\Database\Seeder;

class KpiCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Attendance',
                'name_bn' => null,
                'weight_percentage' => 20.00,
                'calculation_type' => 'Daily Auto',
                'point_setting' => 'System Defined',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Task',
                'name_bn' => null,
                'weight_percentage' => 30.00,
                'calculation_type' => 'Per Task',
                'point_setting' => 'Manager Assign',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Behavior',
                'name_bn' => null,
                'weight_percentage' => 20.00,
                'calculation_type' => 'Monthly Optional',
                'point_setting' => 'Manager Input',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Bonus',
                'name_bn' => null,
                'weight_percentage' => 15.00,
                'calculation_type' => 'Monthly Optional',
                'point_setting' => 'Manager Input',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Penalty',
                'name_bn' => null,
                'weight_percentage' => 15.00,
                'calculation_type' => 'Monthly Optional',
                'point_setting' => 'Manager Input',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \Modules\Kpi\Models\KpiCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('✓ KPI categories seeded successfully!');
    }
}
