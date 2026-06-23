<?php

namespace Modules\Designation\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Designation\Models\Designation;

class DesignationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['title' => 'Chief Executive Officer', 'level' => 1, 'department_id' => null, 'grade_id' => 1, 'is_active' => 1],
            ['title' => 'Chief Technology Officer', 'level' => 1, 'department_id' => 1, 'grade_id' => 1, 'is_active' => 1],
            ['title' => 'Chief Financial Officer', 'level' => 1, 'department_id' => 3, 'grade_id' => 1, 'is_active' => 1],
            ['title' => 'HR Manager', 'level' => 2, 'department_id' => 2, 'grade_id' => 2, 'is_active' => 1],
            ['title' => 'Finance Manager', 'level' => 2, 'department_id' => 3, 'grade_id' => 2, 'is_active' => 1],
            ['title' => 'Marketing Manager', 'level' => 2, 'department_id' => 4, 'grade_id' => 2, 'is_active' => 1],
            ['title' => 'Sales Manager', 'level' => 2, 'department_id' => 5, 'grade_id' => 2, 'is_active' => 1],
            ['title' => 'Operations Manager', 'level' => 2, 'department_id' => 6, 'grade_id' => 2, 'is_active' => 1],
            ['title' => 'Senior Software Engineer', 'level' => 3, 'department_id' => 1, 'grade_id' => 3, 'is_active' => 1],
            ['title' => 'Project Manager', 'level' => 3, 'department_id' => 1, 'grade_id' => 3, 'is_active' => 1],
            ['title' => 'DevOps Engineer', 'level' => 3, 'department_id' => 1, 'grade_id' => 3, 'is_active' => 1],
            ['title' => 'Software Engineer', 'level' => 4, 'department_id' => 1, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'HR Executive', 'level' => 4, 'department_id' => 2, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Accountant', 'level' => 4, 'department_id' => 3, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Marketing Executive', 'level' => 4, 'department_id' => 4, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Sales Executive', 'level' => 4, 'department_id' => 5, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Operations Executive', 'level' => 4, 'department_id' => 6, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Admin Manager', 'level' => 3, 'department_id' => 6, 'grade_id' => 3, 'is_active' => 1],
            ['title' => 'Admin Executive', 'level' => 4, 'department_id' => 6, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'UI/UX Designer', 'level' => 4, 'department_id' => 1, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Quality Assurance Engineer', 'level' => 4, 'department_id' => 1, 'grade_id' => 4, 'is_active' => 1],
            ['title' => 'Junior Software Engineer', 'level' => 5, 'department_id' => 1, 'grade_id' => 5, 'is_active' => 1],
            ['title' => 'Legal Advisor', 'level' => 3, 'department_id' => null, 'grade_id' => 3, 'is_active' => 1],
            ['title' => 'Intern', 'level' => 6, 'department_id' => 1, 'grade_id' => 6, 'is_active' => 1],
        ];

        foreach ($designations as $designation) {
            Designation::updateOrCreate(
                ['title' => $designation['title']],
                $designation
            );
        }

        $this->command->info('✓ Designations seeded: ' . count($designations) . ' records');
    }
}