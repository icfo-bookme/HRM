<?php

namespace Modules\Salary\Database\Seeders;

use Illuminate\Database\Seeder;

class SalaryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SalaryComponentSeeder::class,
            EmployeeSalaryStructureSeeder::class,
            PayrollRunSeeder::class,
        ]);
    }
}
