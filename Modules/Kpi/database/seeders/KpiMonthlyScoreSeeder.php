<?php

namespace Modules\Kpi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class KpiMonthlyScoreSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Skipping KPI monthly score seeder.');
            return;
        }

        $scores = [];
        $now = now();

        foreach ($employees as $employee) {
            for ($monthOffset = 1; $monthOffset <= 3; $monthOffset++) {
                $date = $now->copy()->subMonths($monthOffset);
                $year = $date->year;
                $month = $date->month;

                $workingDays = rand(20, 26);
                $presentDays = rand(18, $workingDays);
                $lateDays = rand(0, 5);
                $attendancePercentage = round(($presentDays / $workingDays) * 100, 2);

                $totalAssignedTasks = rand(5, 15);
                $completedTasks = rand(3, $totalAssignedTasks);
                $taskPercentage = $totalAssignedTasks > 0 ? round(($completedTasks / $totalAssignedTasks) * 100, 2) : 0;

                $behaviorGiven = rand(0, 1);
                $behaviorTarget = 10;
                $behaviorObtained = $behaviorGiven ? rand(5, 10) : 0;
                $behaviorPercentage = $behaviorGiven ? round(($behaviorObtained / $behaviorTarget) * 100, 2) : 0;

                $bonusGiven = rand(0, 1);
                $bonusTarget = 10;
                $bonusObtained = $bonusGiven ? rand(3, 10) : 0;
                $bonusPercentage = $bonusGiven ? round(($bonusObtained / $bonusTarget) * 100, 2) : 0;

                $penaltyGiven = rand(0, 1);
                $penaltyTarget = 10;
                $penaltyObtained = $penaltyGiven ? rand(0, 5) : 0;
                $penaltyPercentage = $penaltyGiven ? round((($penaltyTarget - $penaltyObtained) / $penaltyTarget) * 100, 2) : 100;

                $attendanceWeight = 20;
                $taskWeight = 30;
                $behaviorWeight = 20;
                $bonusWeight = 15;
                $penaltyWeight = 15;

                $attendanceScore = ($attendancePercentage / 100) * $attendanceWeight;
                $taskScore = ($taskPercentage / 100) * $taskWeight;
                $behaviorScore = ($behaviorPercentage / 100) * $behaviorWeight;
                $bonusScore = ($bonusPercentage / 100) * $bonusWeight;
                $penaltyScore = ($penaltyPercentage / 100) * $penaltyWeight;

                $totalTarget = $attendanceWeight + $taskWeight + $behaviorWeight + $bonusWeight + $penaltyWeight;
                $totalObtained = $attendanceScore + $taskScore + $behaviorScore + $bonusScore + $penaltyScore;
                $overallPercentage = round(($totalObtained / $totalTarget) * 100, 2);

                $rating = $this->getRating($overallPercentage);

                $scores[] = [
                    'employee_id' => $employee->id,
                    'year' => $year,
                    'month' => $month,
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'late_days' => $lateDays,
                    'attendance_target' => $attendanceWeight,
                    'attendance_obtained' => round($attendanceScore, 2),
                    'attendance_percentage' => $attendancePercentage,
                    'total_assigned_tasks' => $totalAssignedTasks,
                    'completed_tasks' => $completedTasks,
                    'task_target' => $taskWeight,
                    'task_obtained' => round($taskScore, 2),
                    'task_percentage' => $taskPercentage,
                    'behavior_given' => $behaviorGiven,
                    'behavior_target' => $behaviorTarget,
                    'behavior_obtained' => $behaviorObtained,
                    'behavior_percentage' => $behaviorPercentage,
                    'bonus_given' => $bonusGiven,
                    'bonus_target' => $bonusTarget,
                    'bonus_obtained' => $bonusObtained,
                    'bonus_percentage' => $bonusPercentage,
                    'penalty_given' => $penaltyGiven,
                    'penalty_target' => $penaltyTarget,
                    'penalty_obtained' => $penaltyObtained,
                    'penalty_percentage' => $penaltyPercentage,
                    'total_target' => $totalTarget,
                    'total_obtained' => round($totalObtained, 2),
                    'overall_percentage' => $overallPercentage,
                    'rating' => $rating,
                    'status' => 'Closed',
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        foreach ($scores as $score) {
            DB::table('kpi_monthly_scores')->updateOrInsert(
                ['employee_id' => $score['employee_id'], 'year' => $score['year'], 'month' => $score['month']],
                $score
            );
        }

        $this->command->info('✓ KPI monthly scores seeded successfully!');
    }

    private function getRating(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        return 'D';
    }
}
