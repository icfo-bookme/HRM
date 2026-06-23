<?php

namespace Modules\Kpi\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class KpiMonthlyReviewSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Skipping KPI monthly review seeder.');
            return;
        }

        $reviews = [];
        $now = now();

        // Generate reviews for last 2 months
        foreach ($employees as $employee) {
            for ($monthOffset = 1; $monthOffset <= 2; $monthOffset++) {
                $date = $now->copy()->subMonths($monthOffset);
                $year = $date->year;
                $month = $date->month;

                // Randomly decide which optional scores to give
                $giveBehavior = (bool) rand(0, 1);
                $giveBonus = (bool) rand(0, 1);
                $givePenalty = (bool) rand(0, 1);

                $reviews[] = [
                    'employee_id' => $employee->id,
                    'reviewer_id' => 1, // Admin user
                    'year' => $year,
                    'month' => $month,
                    'give_behavior' => $giveBehavior,
                    'behavior_score' => $giveBehavior ? round(rand(50, 100) / 10, 1) : null, // 5.0 - 10.0
                    'behavior_remarks' => $giveBehavior ? $this->getRandomBehaviorRemark() : null,
                    'give_bonus' => $giveBonus,
                    'bonus_score' => $giveBonus ? round(rand(30, 100) / 10, 1) : null, // 3.0 - 10.0
                    'bonus_remarks' => $giveBonus ? $this->getRandomBonusRemark() : null,
                    'give_penalty' => $givePenalty,
                    'penalty_score' => $givePenalty ? round(rand(0, 70) / 10, 1) : null, // 0.0 - 7.0
                    'penalty_remarks' => $givePenalty ? $this->getRandomPenaltyRemark() : null,
                    'status' => 'Approved',
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        DB::table('kpi_monthly_reviews')->insert($reviews);

        $this->command->info('✓ KPI monthly reviews seeded for ' . count($employees) . ' employees (2 months)');
    }

    private function getRandomBehaviorRemark(): string
    {
        $remarks = [
            'Demonstrated excellent teamwork and cooperation throughout the month.',
            'Showed great leadership qualities and helped junior team members.',
            'Maintained positive attitude and professional behavior at all times.',
            'Actively participated in team meetings and contributed valuable ideas.',
            'Displayed exceptional customer service skills.',
            'Good behavior and punctuality observed consistently.',
        ];
        return $remarks[array_rand($remarks)];
    }

    private function getRandomBonusRemark(): string
    {
        $remarks = [
            'Exceeded monthly targets by 20%. Exceptional performance bonus awarded.',
            'Took initiative to complete pending tasks ahead of schedule.',
            'Volunteered for additional responsibilities during team member absence.',
            'Successfully led a critical project to completion.',
            'Achieved highest customer satisfaction rating in the department.',
            'Implemented process improvements that saved significant time.',
        ];
        return $remarks[array_rand($remarks)];
    }

    private function getRandomPenaltyRemark(): string
    {
        $remarks = [
            'Repeated late attendance without valid reason.',
            'Missed important deadlines for assigned tasks.',
            'Failure to follow established procedures.',
            'Incomplete documentation submitted for project.',
            'Unsatisfactory response time to customer queries.',
            'Did not attend mandatory team meetings.',
        ];
        return $remarks[array_rand($remarks)];
    }
}