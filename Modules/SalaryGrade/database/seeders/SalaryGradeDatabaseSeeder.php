<?php

namespace Modules\SalaryGrade\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SalaryGrade\Models\SalaryGrade;

class SalaryGradeDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['name' => 'Executive Level', 'min_salary' => 200000, 'max_salary' => 500000, 'is_active' => 1],
            ['name' => 'Senior Management', 'min_salary' => 120000, 'max_salary' => 200000, 'is_active' => 1],
            ['name' => 'Mid Management', 'min_salary' => 70000, 'max_salary' => 120000, 'is_active' => 1],
            ['name' => 'Junior Executive', 'min_salary' => 40000, 'max_salary' => 70000, 'is_active' => 1],
            ['name' => 'Entry Level', 'min_salary' => 20000, 'max_salary' => 40000, 'is_active' => 1],
            ['name' => 'Intern/Trainee', 'min_salary' => 8000, 'max_salary' => 20000, 'is_active' => 1],
        ];

        foreach ($grades as $grade) {
            SalaryGrade::updateOrCreate(
                ['name' => $grade['name']],
                $grade
            );
        }

        $this->command->info('✓ Salary grades seeded: ' . count($grades) . ' records');
    }
}