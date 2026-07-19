<?php

namespace Modules\Leave\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Leave\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Seed the leave_types table with standard leave types.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'description' => 'Standard annual leave for all employees.',
                'days_per_year' => 15,
                'is_paid' => true,
                'is_half_day_allowed' => false,
                'carry_forward' => true,
                'max_carry_days' => 5,
                'max_consecutive_days' => 30,
                'requires_document' => false,
                'min_days_notice' => 1,
                'applicable_gender' => 'All',
                'color_code' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Leave for medical reasons. Medical certificate required for more than 3 consecutive days.',
                'days_per_year' => 10,
                'is_paid' => true,
                'is_half_day_allowed' => true,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 5,
                'requires_document' => true,
                'min_days_notice' => 0,
                'applicable_gender' => 'All',
                'color_code' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'Casual Leave',
                'description' => 'Short leave for personal tasks and emergencies.',
                'days_per_year' => 5,
                'is_paid' => true,
                'is_half_day_allowed' => true,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 3,
                'requires_document' => false,
                'min_days_notice' => 0,
                'applicable_gender' => 'All',
                'color_code' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => 'Maternity leave for female employees as per company policy.',
                'days_per_year' => 120,
                'is_paid' => true,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 120,
                'requires_document' => true,
                'min_days_notice' => 30,
                'applicable_gender' => 'Female',
                'color_code' => '#EC4899',
                'is_active' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => 'Paternity leave for new fathers.',
                'days_per_year' => 7,
                'is_paid' => true,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 7,
                'requires_document' => true,
                'min_days_notice' => 7,
                'applicable_gender' => 'Male',
                'color_code' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Marriage Leave',
                'description' => "Leave for employee's own wedding.",
                'days_per_year' => 5,
                'is_paid' => true,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 5,
                'requires_document' => true,
                'min_days_notice' => 7,
                'applicable_gender' => 'All',
                'color_code' => '#F43F5E',
                'is_active' => true,
            ],
            [
                'name' => 'Bereavement Leave',
                'description' => 'Leave in case of death of an immediate family member.',
                'days_per_year' => 3,
                'is_paid' => true,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 3,
                'requires_document' => false,
                'min_days_notice' => 0,
                'applicable_gender' => 'All',
                'color_code' => '#6B7280',
                'is_active' => true,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Unpaid leave for educational purposes and examinations.',
                'days_per_year' => 10,
                'is_paid' => false,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 10,
                'requires_document' => true,
                'min_days_notice' => 14,
                'applicable_gender' => 'All',
                'color_code' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Compensatory Off',
                'description' => 'Compensatory time off for overtime or holiday work.',
                'days_per_year' => 0,
                'is_paid' => true,
                'is_half_day_allowed' => true,
                'carry_forward' => true,
                'max_carry_days' => 30,
                'max_consecutive_days' => 0,
                'requires_document' => false,
                'min_days_notice' => 0,
                'applicable_gender' => 'All',
                'color_code' => '#14B8A6',
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'description' => 'Leave without pay for personal reasons when other leave types are exhausted.',
                'days_per_year' => 0,
                'is_paid' => false,
                'is_half_day_allowed' => false,
                'carry_forward' => false,
                'max_carry_days' => 0,
                'max_consecutive_days' => 0,
                'requires_document' => true,
                'min_days_notice' => 3,
                'applicable_gender' => 'All',
                'color_code' => '#9CA3AF',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Leave types seeded successfully: ' . count($leaveTypes) . ' types created.');
    }
}