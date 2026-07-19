<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Note: Dependency seeders (Company, Branch, Department, SalaryGrade, Shift, Designation)
     * are called from the main DatabaseSeeder to maintain proper order.
     */
    public function run(): void
    {
        $this->call([
            SkillCategorySeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
