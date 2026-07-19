<?php

namespace Modules\Salary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Salary\Models\SalaryComponent;

class EmployeeSalaryStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active employees
        $employees = DB::table('employees')->select('id', 'employee_code')->get();

        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Skipping EmployeeSalaryStructureSeeder.');
            return;
        }

        $user = DB::table('users')->first();
        $createdBy = $user ? $user->id : null;

        $now = now();
        $effectiveFrom = now()->startOfMonth()->subMonth();

        foreach ($employees as $employee) {
            // Determine a basic salary based on employee code hash for consistency
            $basicSalary = $this->getBasicSalaryForEmployee($employee->employee_code);

            // Build salary structure entries
            $structures = [
                // Earning Components
                [
                    'component_name' => 'Basic Salary',
                    'amount'         => $basicSalary,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'House Rent Allowance',
                    'amount'         => 50, // 50% of basic
                    'is_percentage'  => true,
                ],
                [
                    'component_name' => 'Medical Allowance',
                    'amount'         => 1500,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'Conveyance Allowance',
                    'amount'         => 500,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'Dearness Allowance',
                    'amount'         => 2000,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'Special Allowance',
                    'amount'         => 3000,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'City Compensatory Allowance',
                    'amount'         => 1000,
                    'is_percentage'  => false,
                ],

                // Deduction Components
                [
                    'component_name' => 'Provident Fund (PF)',
                    'amount'         => 12, // 12% of basic
                    'is_percentage'  => true,
                ],
                [
                    'component_name' => 'Professional Tax',
                    'amount'         => 200,
                    'is_percentage'  => false,
                ],
                [
                    'component_name' => 'Income Tax (TDS)',
                    'amount'         => 10, // 10% of gross
                    'is_percentage'  => true,
                ],
            ];

            foreach ($structures as $structure) {
                $component = SalaryComponent::where('name', $structure['component_name'])->first();
                if (!$component) {
                    continue;
                }

                DB::table('employee_salary_structure')->updateOrInsert(
                    [
                        'employee_id'  => $employee->id,
                        'component_id' => $component->id,
                        'effective_to' => null,
                    ],
                    [
                        'amount'         => $structure['amount'],
                        'effective_from' => $effectiveFrom,
                        'effective_to'   => null,
                        'is_percentage'  => $structure['is_percentage'],
                        'created_by'     => $createdBy,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]
                );
            }
        }

        $this->command->info(count($employees) . ' employees have been assigned salary structures.');
    }

    /**
     * Get a realistic basic salary based on employee code.
     */
    private function getBasicSalaryForEmployee(string $employeeCode): float
    {
        // Use employee code to determine salary range
        $salaries = [
            15000, 18000, 20000, 22000, 25000, 28000,
            30000, 35000, 40000, 45000, 50000, 55000,
            60000, 65000, 70000, 75000, 80000, 90000,
        ];

        // Use the last digits of employee code to pick a salary
        $index = (int) substr($employeeCode, -2) % count($salaries);
        return $salaries[$index];
    }
}