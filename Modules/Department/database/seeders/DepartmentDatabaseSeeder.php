<?php

namespace Modules\Department\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Department\Models\Department;

class DepartmentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Department::updateOrCreate(
            ['code' => 'IT-001'],
            [
                'branch_id' => 1,
                'name' => 'Information Technology',
                'description' => 'Software development, infrastructure and IT operations',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'HR-001'],
            [
                'branch_id' => 1,
                'name' => 'Human Resources',
                'description' => 'Recruitment, payroll, employee relations and training',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'FIN-001'],
            [
                'branch_id' => 1,
                'name' => 'Finance & Accounts',
                'description' => 'Financial management, accounting and reporting',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'MKT-001'],
            [
                'branch_id' => 1,
                'name' => 'Marketing',
                'description' => 'Brand management, digital marketing and communications',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'SAL-001'],
            [
                'branch_id' => 1,
                'name' => 'Sales',
                'description' => 'Sales operations and business development',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'OPS-001'],
            [
                'branch_id' => 1,
                'name' => 'Operations',
                'description' => 'Supply chain, logistics and administration',
                'is_active' => true,
                'sort_order' => 6,
            ]
        );

        $this->command->info('✓ Departments seeded: 6 records');
    }
}