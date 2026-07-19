<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->command->info('--- Seeding Core Data ---');

        // Dependency order: Company -> Branch -> Department -> SalaryGrade -> Shift -> Designation
        $this->call([
            \Modules\Company\Database\Seeders\CompanyDatabaseSeeder::class,
            \Modules\Branch\Database\Seeders\BranchDatabaseSeeder::class,
            \Modules\Department\Database\Seeders\DepartmentDatabaseSeeder::class,
            \Modules\SalaryGrade\Database\Seeders\SalaryGradeDatabaseSeeder::class,
            \Modules\Shift\Database\Seeders\ShiftDatabaseSeeder::class,
            \Modules\Designation\Database\Seeders\DesignationDatabaseSeeder::class,
        ]);

        $this->command->info('--- Seeding Employee Data ---');
        $this->call([
            \Modules\Employee\Database\Seeders\EmployeeDatabaseSeeder::class,
        ]);

        $this->command->info('--- Seeding Notice Data ---');
        $this->call([
            \Modules\Notice\Database\Seeders\NoticeDatabaseSeeder::class,
        ]);

        $this->command->info('--- Seeding Roles & Permissions ---');
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $this->command->info('--- Seeding Leave Data ---');
        $this->call([
            \Modules\Leave\Database\Seeders\LeaveDatabaseSeeder::class,
        ]);

        $this->command->info('--- Seeding KPI Data ---');
        $this->call([
            \Modules\Kpi\Database\Seeders\KpiDatabaseSeeder::class,
        ]);

        $this->command->info('--- Seeding Users ---');
        $this->seedUsers();
    }

    private function seedUsers(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $employeeRole = Role::where('slug', 'employee')->first();

        $users = [
            [
                'name'       => 'Admin User',
                'email'      => 'admin@company.com',
                'password'   => Hash::make('password'),
                'employee_id'=> 1,
                'role_id'    => $adminRole?->id,
            ],
            [
                'name'       => 'Manager User',
                'email'      => 'manager@company.com',
                'password'   => Hash::make('password'),
                'employee_id'=> 2,
                'role_id'    => $managerRole?->id,
            ],
            [
                'name'       => 'Employee User',
                'email'      => 'employee@company.com',
                'password'   => Hash::make('password'),
                'employee_id'=> 3,
                'role_id'    => $employeeRole?->id,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✓ Users seeded: ' . count($users) . ' records (password: password)');
    }
}