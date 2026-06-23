<?php

namespace Modules\Kpi\Services;

use Illuminate\Support\Facades\DB;
use Modules\Kpi\Models\KpiMonthlyScore;
use Modules\Kpi\Models\KpiMonthlyReview;
use Modules\Kpi\Models\KpiTask;
use Modules\Kpi\Models\KpiDailyTracking;
use Modules\Employee\Models\Employee;

class KpiMonthlyService
{
    protected KpiDailyService $dailyService;

    public function __construct(KpiDailyService $dailyService)
    {
        $this->dailyService = $dailyService;
    }

    /**
     * Calculate and close monthly score for an employee
     */
    public function calculateMonthlyScore(int $employeeId, int $year, int $month): array
    {
        try {
            return DB::transaction(function () use ($employeeId, $year, $month) {
                // 1. Get attendance summary from daily tracking
                $attendanceSummary = $this->dailyService->getMonthlyTrackingSummary($employeeId, $year, $month);

                // 2. Get task summary
                $taskSummary = $this->getTaskSummary($employeeId, $year, $month);

                // 3. Get monthly review (behavior/bonus/penalty)
                $review = KpiMonthlyReview::where('employee_id', $employeeId)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();

                // 4. Calculate behavior/bonus/penalty
                $behaviorData = $this->getOptionalScoreData($review, 'behavior');
                $bonusData = $this->getOptionalScoreData($review, 'bonus');
                $penaltyData = $this->getOptionalScoreData($review, 'penalty');

                // 5. Calculate totals
                $totalTarget = $attendanceSummary['attendance_target']
                    + $taskSummary['task_target']
                    + $behaviorData['target']
                    + $bonusData['target']
                    + $penaltyData['target'];

                $totalObtained = $attendanceSummary['attendance_obtained']
                    + $taskSummary['task_obtained']
                    + $behaviorData['obtained']
                    + $bonusData['obtained']
                    + $penaltyData['obtained'];

                $overallPercentage = $totalTarget > 0
                    ? round(($totalObtained / $totalTarget) * 100, 2)
                    : 0;

                $rating = $this->calculateRating($overallPercentage);

                // 6. Save monthly score
                $monthlyScore = KpiMonthlyScore::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'working_days' => $attendanceSummary['working_days'],
                        'present_days' => $attendanceSummary['present_days'],
                        'late_days' => $attendanceSummary['late_days'],
                        'attendance_target' => $attendanceSummary['attendance_target'],
                        'attendance_obtained' => $attendanceSummary['attendance_obtained'],
                        'attendance_percentage' => $attendanceSummary['attendance_percentage'],
                        'total_assigned_tasks' => $taskSummary['total_assigned'],
                        'completed_tasks' => $taskSummary['completed'],
                        'task_target' => $taskSummary['task_target'],
                        'task_obtained' => $taskSummary['task_obtained'],
                        'task_percentage' => $taskSummary['task_percentage'],
                        'behavior_given' => $behaviorData['given'],
                        'behavior_target' => $behaviorData['target'],
                        'behavior_obtained' => $behaviorData['obtained'],
                        'behavior_percentage' => $behaviorData['percentage'],
                        'bonus_given' => $bonusData['given'],
                        'bonus_target' => $bonusData['target'],
                        'bonus_obtained' => $bonusData['obtained'],
                        'bonus_percentage' => $bonusData['percentage'],
                        'penalty_given' => $penaltyData['given'],
                        'penalty_target' => $penaltyData['target'],
                        'penalty_obtained' => $penaltyData['obtained'],
                        'penalty_percentage' => $penaltyData['percentage'],
                        'total_target' => $totalTarget,
                        'total_obtained' => $totalObtained,
                        'overall_percentage' => $overallPercentage,
                        'rating' => $rating,
                        'status' => 'Closed',
                    ]
                );

                return [
                    'status' => 'success',
                    'message' => "KPI score calculated for {$year}-{$month}",
                    'data' => $monthlyScore,
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to calculate monthly score: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get task summary for the month
     */
    private function getTaskSummary(int $employeeId, int $year, int $month): array
    {
        $tasks = KpiTask::where('employee_id', $employeeId)
            ->whereYear('assigned_date', $year)
            ->whereMonth('assigned_date', $month)
            ->get();

        $totalAssigned = $tasks->count();
        $completed = $tasks->where('status', 'Completed')->count();
        $taskTarget = $tasks->sum('target_score');
        $taskObtained = $tasks->where('status', 'Completed')->sum('obtained_score');

        return [
            'total_assigned' => $totalAssigned,
            'completed' => $completed,
            'task_target' => $taskTarget,
            'task_obtained' => $taskObtained,
            'task_percentage' => $taskTarget > 0
                ? round(($taskObtained / $taskTarget) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get optional score data (behavior/bonus/penalty)
     */
    private function getOptionalScoreData(?KpiMonthlyReview $review, string $type): array
    {
        if (!$review) {
            return [
                'given' => false,
                'target' => 0,
                'obtained' => 0,
                'percentage' => 0,
            ];
        }

        $givenField = "give_{$type}";
        $scoreField = "{$type}_score";

        if (!$review->$givenField || $review->$scoreField === null) {
            return [
                'given' => false,
                'target' => 0,
                'obtained' => 0,
                'percentage' => 0,
            ];
        }

        $obtained = (float) $review->$scoreField;
        $target = 10; // default max

        return [
            'given' => true,
            'target' => $target,
            'obtained' => $obtained,
            'percentage' => $target > 0 ? round(($obtained / $target) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate rating based on percentage
     */
    public function calculateRating(float $percentage): ?string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 0  => 'D',
            default           => null,
        };
    }

    /**
     * Get employee monthly performance
     */
    public function getEmployeeMonthlyPerformance(int $employeeId, int $year, int $month): array
    {
        $score = KpiMonthlyScore::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$score) {
            return [
                'status' => 'success',
                'data' => null,
                'message' => 'No KPI score found for this period',
            ];
        }

        $score->load('employee.personalInfo');
        $data = $score->toArray();

        // Add computed field for bonus+penalty combined percentage (for views)
        $bonusTarget = (float) ($data['bonus_target'] ?? 0);
        $bonusObtained = (float) ($data['bonus_obtained'] ?? 0);
        $penaltyTarget = (float) ($data['penalty_target'] ?? 0);
        $penaltyObtained = (float) ($data['penalty_obtained'] ?? 0);
        $combinedTarget = $bonusTarget + $penaltyTarget;
        $combinedObtained = $bonusObtained + $penaltyObtained;
        $data['bonus_penalty_percentage'] = $combinedTarget > 0 ? round(($combinedObtained / $combinedTarget) * 100, 1) : 0;

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    /**
     * Get monthly performance for all employees (manager view)
     */
    public function getMonthlyPerformanceReport(int $year, int $month, ?int $departmentId = null): array
    {
        $query = KpiMonthlyScore::with(['employee.personalInfo', 'employee.department'])
            ->where('year', $year)
            ->where('month', $month);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $scores = $query->orderBy('overall_percentage', 'desc')->get();

        $summary = [
            'total_employees' => $scores->count(),
            'average_percentage' => $scores->avg('overall_percentage'),
            'highest_score' => $scores->max('overall_percentage'),
            'lowest_score' => $scores->min('overall_percentage'),
            'rating_distribution' => [
                'A+' => $scores->where('rating', 'A+')->count(),
                'A' => $scores->where('rating', 'A')->count(),
                'B+' => $scores->where('rating', 'B+')->count(),
                'B' => $scores->where('rating', 'B')->count(),
                'C' => $scores->where('rating', 'C')->count(),
                'D' => $scores->where('rating', 'D')->count(),
            ],
        ];

        return [
            'status' => 'success',
            'summary' => $summary,
            'scores' => $scores,
        ];
    }
}
