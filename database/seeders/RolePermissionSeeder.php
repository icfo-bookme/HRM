<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. Create Permissions
        // ==========================================
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'Dashboard'],

            // Employee Management
            ['name' => 'View Employees', 'slug' => 'employees.view', 'group' => 'Employees'],
            ['name' => 'Create Employees', 'slug' => 'employees.create', 'group' => 'Employees'],
            ['name' => 'Edit Employees', 'slug' => 'employees.edit', 'group' => 'Employees'],
            ['name' => 'Delete Employees', 'slug' => 'employees.delete', 'group' => 'Employees'],
            ['name' => 'View Departments', 'slug' => 'departments.view', 'group' => 'Employees'],
            ['name' => 'Manage Departments', 'slug' => 'departments.manage', 'group' => 'Employees'],
            ['name' => 'View Skill Categories', 'slug' => 'skills.view', 'group' => 'Employees'],
            ['name' => 'Manage Skill Categories', 'slug' => 'skills.manage', 'group' => 'Employees'],
            ['name' => 'View Employee Weekends', 'slug' => 'weekends.view', 'group' => 'Employees'],
            ['name' => 'Manage Employee Weekends', 'slug' => 'weekends.manage', 'group' => 'Employees'],
            ['name' => 'View Attendance Rules', 'slug' => 'attendance-rules.view', 'group' => 'Employees'],
            ['name' => 'Manage Attendance Rules', 'slug' => 'attendance-rules.manage', 'group' => 'Employees'],

            // Shifts
            ['name' => 'View Shifts', 'slug' => 'shifts.view', 'group' => 'Shifts'],
            ['name' => 'Manage Shifts', 'slug' => 'shifts.manage', 'group' => 'Shifts'],

            // Notice
            ['name' => 'View Notices', 'slug' => 'notices.view', 'group' => 'Notice'],
            ['name' => 'Manage Notices', 'slug' => 'notices.manage', 'group' => 'Notice'],

            // Leave
            ['name' => 'Apply Leave', 'slug' => 'leave.apply', 'group' => 'Leave'],
            ['name' => 'View My Leave', 'slug' => 'leave.my', 'group' => 'Leave'],
            ['name' => 'View All Leaves', 'slug' => 'leave.view-all', 'group' => 'Leave'],
            ['name' => 'Manage Leave Types', 'slug' => 'leave.manage-types', 'group' => 'Leave'],
            ['name' => 'Manage Leave Balance', 'slug' => 'leave.manage-balance', 'group' => 'Leave'],
            ['name' => 'Manage Leave Encashment', 'slug' => 'leave.encashment', 'group' => 'Leave'],

            // Holiday
            ['name' => 'View Holidays', 'slug' => 'holidays.view', 'group' => 'Holiday'],
            ['name' => 'Manage Holidays', 'slug' => 'holidays.manage', 'group' => 'Holiday'],
            ['name' => 'View Holiday Calendar', 'slug' => 'holidays.calendar', 'group' => 'Holiday'],
            ['name' => 'Assign Holidays', 'slug' => 'holidays.assign', 'group' => 'Holiday'],

            // Attendance
            ['name' => 'Add Attendance', 'slug' => 'attendance.create', 'group' => 'Attendance'],
            ['name' => 'View Attendance List', 'slug' => 'attendance.view', 'group' => 'Attendance'],
            ['name' => 'Manage Attendance Devices', 'slug' => 'attendance.devices', 'group' => 'Attendance'],

            // Reports
            ['name' => 'View Attendance Report', 'slug' => 'reports.attendance', 'group' => 'Reports'],
            ['name' => 'View Payroll Report', 'slug' => 'reports.payroll', 'group' => 'Reports'],
            ['name' => 'View HR Analytics', 'slug' => 'reports.hr-analytics', 'group' => 'Reports'],

            // KPI
            ['name' => 'View KPI Dashboard', 'slug' => 'kpi.dashboard', 'group' => 'KPI'],
            ['name' => 'View Daily Performance', 'slug' => 'kpi.daily', 'group' => 'KPI'],
            ['name' => 'View Monthly Performance', 'slug' => 'kpi.monthly', 'group' => 'KPI'],
            ['name' => 'Manage KPI Tasks', 'slug' => 'kpi.tasks', 'group' => 'KPI'],
            ['name' => 'Manage KPI Reviews', 'slug' => 'kpi.reviews', 'group' => 'KPI'],
            ['name' => 'Manage KPI Settings', 'slug' => 'kpi.settings', 'group' => 'KPI'],

            // Salary
            ['name' => 'View Salary Components', 'slug' => 'salary.components', 'group' => 'Salary'],
            ['name' => 'View Employee Salary Structure', 'slug' => 'salary.structure', 'group' => 'Salary'],
            ['name' => 'View Payroll Runs', 'slug' => 'salary.payroll-view', 'group' => 'Salary'],
            ['name' => 'Generate Payroll', 'slug' => 'salary.payroll-generate', 'group' => 'Salary'],

            // Loan
            ['name' => 'Apply Loan', 'slug' => 'loan.apply', 'group' => 'Loan'],
            ['name' => 'View My Loans', 'slug' => 'loan.my', 'group' => 'Loan'],
            ['name' => 'View All Loans', 'slug' => 'loan.view-all', 'group' => 'Loan'],

            // Settings
            ['name' => 'User Management', 'slug' => 'settings.users', 'group' => 'Settings'],
            ['name' => 'Roles & Permissions', 'slug' => 'settings.roles', 'group' => 'Settings'],
            ['name' => 'System Settings', 'slug' => 'settings.system', 'group' => 'Settings'],
            ['name' => 'Manage Fiscal Years', 'slug' => 'settings.fiscal-years', 'group' => 'Settings'],

            // Administration
            ['name' => 'Company Setup', 'slug' => 'admin.company', 'group' => 'Administration'],
            ['name' => 'Manage Branches', 'slug' => 'admin.branches', 'group' => 'Administration'],
            ['name' => 'Manage Salary Grades', 'slug' => 'admin.salary-grades', 'group' => 'Administration'],
            ['name' => 'Manage Designations', 'slug' => 'admin.designations', 'group' => 'Administration'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // ==========================================
        // 2. Create Roles
        // ==========================================

        // --- Admin Role: ALL permissions ---
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full system access. Can manage all modules, settings, and users.',
                'is_system' => true,
            ]
        );
        $adminRole->syncPermissions(Permission::pluck('id')->toArray());

        // --- Manager Role: Operational permissions (no system settings/roles) ---
        $managerPermissions = Permission::whereNotIn('group', ['Settings'])->pluck('id')->toArray();
        $managerRole = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Operational access to HR, attendance, leave, KPI, salary, loan, and reports.',
                'is_system' => true,
            ]
        );
        $managerRole->syncPermissions($managerPermissions);

        // --- Employee Role: Self-service only ---
        $employeePermissionSlugs = [
            'dashboard.view',
            'notices.view',
            'leave.apply',
            'leave.my',
            'holidays.view',
            'holidays.calendar',
            'kpi.dashboard',
            'kpi.daily',
            'kpi.monthly',
            'loan.apply',
            'loan.my',
        ];
        $employeePermissionIds = Permission::whereIn('slug', $employeePermissionSlugs)->pluck('id')->toArray();
        $employeeRole = Role::firstOrCreate(
            ['slug' => 'employee'],
            [
                'name' => 'Employee',
                'description' => 'Self-service access. Can apply leave, view own data, and access basic features.',
                'is_system' => true,
            ]
        );
        $employeeRole->syncPermissions($employeePermissionIds);

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('  - Admin: ' . $adminRole->permissions()->count() . ' permissions');
        $this->command->info('  - Manager: ' . $managerRole->permissions()->count() . ' permissions');
        $this->command->info('  - Employee: ' . $employeeRole->permissions()->count() . ' permissions');
    }
}