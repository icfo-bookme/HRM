<?php

return [
    'name' => 'KPI',

    /*
    |--------------------------------------------------------------------------
    | Default KPI Configuration
    |--------------------------------------------------------------------------
    */
    'categories' => [
        [
            'name' => 'Attendance',
            'name_bn' => null,
            'weight_percentage' => 20.00,
            'calculation_type' => 'Daily Auto',
            'point_setting' => 'System Defined',
            'sort_order' => 1,
        ],
        [
            'name' => 'Task',
            'name_bn' => null,
            'weight_percentage' => 30.00,
            'calculation_type' => 'Per Task',
            'point_setting' => 'Manager Assign',
            'sort_order' => 2,
        ],
        [
            'name' => 'Behavior',
            'name_bn' => null,
            'weight_percentage' => 20.00,
            'calculation_type' => 'Monthly Optional',
            'point_setting' => 'Manager Input',
            'sort_order' => 3,
        ],
        [
            'name' => 'Bonus',
            'name_bn' => null,
            'weight_percentage' => 15.00,
            'calculation_type' => 'Monthly Optional',
            'point_setting' => 'Manager Input',
            'sort_order' => 4,
        ],
        [
            'name' => 'Penalty',
            'name_bn' => null,
            'weight_percentage' => 15.00,
            'calculation_type' => 'Monthly Optional',
            'point_setting' => 'Manager Input',
            'sort_order' => 5,
        ],
    ],

    'indicators' => [
        [
            'category_key' => 'Attendance',
            'key' => 'present',
            'name' => 'Present',
            'name_bn' => null,
            'weight_percentage' => 50.00,
            'point_per_unit' => 1.00,
            'default_max_score' => null,
            'count_behavior' => 'Always Count',
        ],
        [
            'category_key' => 'Attendance',
            'key' => 'late',
            'name' => 'Late',
            'name_bn' => null,
            'weight_percentage' => 50.00,
            'point_per_unit' => -2.00,
            'default_max_score' => null,
            'count_behavior' => 'Always Count',
        ],
        [
            'category_key' => 'Task',
            'key' => 'task',
            'name' => 'Task',
            'name_bn' => null,
            'weight_percentage' => 100.00,
            'point_per_unit' => null,
            'default_max_score' => null,
            'count_behavior' => 'Always Count',
        ],
        [
            'category_key' => 'Behavior',
            'key' => 'behavior',
            'name' => 'Behavior',
            'name_bn' => null,
            'weight_percentage' => 100.00,
            'point_per_unit' => null,
            'default_max_score' => 10.00,
            'count_behavior' => 'Optional Count',
        ],
        [
            'category_key' => 'Bonus',
            'key' => 'bonus',
            'name' => 'Bonus',
            'name_bn' => null,
            'weight_percentage' => 100.00,
            'point_per_unit' => null,
            'default_max_score' => 10.00,
            'count_behavior' => 'Optional Count',
        ],
        [
            'category_key' => 'Penalty',
            'key' => 'penalty',
            'name' => 'Penalty',
            'name_bn' => null,
            'weight_percentage' => 100.00,
            'point_per_unit' => null,
            'default_max_score' => 10.00,
            'count_behavior' => 'Optional Count',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rating Scale
    |--------------------------------------------------------------------------
    */
    'ratings' => [
        ['grade' => 'A+', 'min_percentage' => 90, 'label' => 'Outstanding'],
        ['grade' => 'A', 'min_percentage' => 80, 'label' => 'Excellent'],
        ['grade' => 'B+', 'min_percentage' => 70, 'label' => 'Good'],
        ['grade' => 'B', 'min_percentage' => 60, 'label' => 'Satisfactory'],
        ['grade' => 'C', 'min_percentage' => 50, 'label' => 'Needs Improvement'],
        ['grade' => 'D', 'min_percentage' => 0, 'label' => 'Poor'],
    ],
];
