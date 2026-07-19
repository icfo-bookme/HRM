<?php

namespace Modules\Kpi\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Kpi\Models\KpiDailyTracking;
use Modules\Attendance\Models\Attendance;

class KpiDailyService
{
    /**
     * Process daily attendance for all employees
     */
    public function processDailyAttendance(?Carbon $date = null): array
    {
        $date = $date ?: Carbon::today();
        $processed = 0;
        $errors = 0;

        try {
            $attendances = Attendance::whereDate('attendance_date', $date)
                ->whereIn('attendance_status', ['Present', 'Late', 'Half Day'])
                ->get();

            foreach ($attendances as $attendance) {
                try {
                    $this->processEmployeeDaily($attendance->employee_id, $date, $attendance);
                    $processed++;
                } catch (\Exception $e) {
                    $errors++;
                    \Log::error("KPI Daily processing failed for employee {$attendance->employee_id}: " . $e->getMessage());
                }
            }

            return [
                'status' => 'success',
                'date' => $date->format('Y-m-d'),
                'processed' => $processed,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to process daily attendance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process single employee daily KPI
     */
    public function processEmployeeDaily(int $employeeId, Carbon $date, ?Attendance $attendance = null): KpiDailyTracking
    {
        $isWorkingDay = $this->isWorkingDay($date);
        $isPresent = $attendance && in_array($attendance->attendance_status, ['Present', 'Late', 'Half Day']);
        $isLate = $attendance && $attendance->is_late;

        $presentTarget = $isWorkingDay ? 1 : 0;
        $presentObtained = ($isPresent && !$isLate) ? 1 : 0;
        $lateTarget = $isWorkingDay ? 1 : 0;
        $lateObtained = $isLate ? -2 : 0;

        $dailyTarget = $presentTarget + $lateTarget;
        $dailyObtained = $presentObtained + $lateObtained;
        $dailyPercentage = $dailyTarget > 0 ? round(($dailyObtained / $dailyTarget) * 100, 2) : 0;

        return KpiDailyTracking::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'tracking_date' => $date->format('Y-m-d'),
            ],
            [
                'is_working_day' => $isWorkingDay,
                'is_present' => $isPresent,
                'is_late' => $isLate,
                'present_target' => $presentTarget,
                'present_obtained' => $presentObtained,
                'late_target' => $lateTarget,
                'late_obtained' => $lateObtained,
                'daily_target' => $dailyTarget,
                'daily_obtained' => $dailyObtained,
                'daily_percentage' => $dailyPercentage,
            ]
        );
    }

    /**
     * Get daily performance for an employee
     */
    public function getEmployeeDailyPerformance(int $employeeId, ?string $date = null): array
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');

        $tracking = KpiDailyTracking::where('employee_id', $employeeId)
            ->where('tracking_date', $date)
            ->first();

        if (!$tracking) {
            return [
                'status' => 'success',
                'data' => [
                    'date' => $date,
                    'is_working_day' => false,
                    'is_present' => false,
                    'is_late' => false,
                    'daily_target' => 0,
                    'daily_obtained' => 0,
                    'daily_percentage' => 0,
                    'overall_percentage' => 0,
                    'total_target' => 0,
                    'total_obtained' => 0,
                    'indicators' => [],
                ],
            ];
        }

        $data = $tracking->toArray();
        $data['overall_percentage'] = (float) $tracking->daily_percentage;
        $data['total_target'] = (float) $tracking->daily_target;
        $data['total_obtained'] = (float) $tracking->daily_obtained;
        $data['indicators'] = [
            [
                'name' => 'Present',
                'target' => (float) $tracking->present_target,
                'obtained' => (float) $tracking->present_obtained,
                'percentage' => $tracking->present_target > 0 ? round(($tracking->present_obtained / $tracking->present_target) * 100, 1) : 0,
                'remarks' => $tracking->is_present ? 'Present' : 'Absent',
            ],
            [
                'name' => 'Late',
                'target' => (float) $tracking->late_target,
                'obtained' => (float) $tracking->late_obtained,
                'percentage' => $tracking->late_target > 0 ? round(($tracking->late_obtained / $tracking->late_target) * 100, 1) : 0,
                'remarks' => $tracking->is_late ? 'Late arrival' : 'On time',
            ],
        ];

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    /**
     * Get monthly daily tracking summary
     */
    public function getMonthlyTrackingSummary(int $employeeId, int $year, int $month): array
    {
        $trackings = KpiDailyTracking::where('employee_id', $employeeId)
            ->whereYear('tracking_date', $year)
            ->whereMonth('tracking_date', $month)
            ->get();

        $totalTarget = $trackings->sum('daily_target');
        $totalObtained = $trackings->sum('daily_obtained');
        $workingDays = $trackings->where('is_working_day', true)->count();
        $presentDays = $trackings->where('is_present', true)->count();
        $lateDays = $trackings->where('is_late', true)->count();

        return [
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'attendance_target' => $totalTarget,
            'attendance_obtained' => $totalObtained,
            'attendance_percentage' => $totalTarget > 0 ? round(($totalObtained / $totalTarget) * 100, 2) : 0,
            'daily_records' => $trackings,
        ];
    }

    /**
     * Check if a date is a working day (not holiday/weekend)
     */
    private function isWorkingDay(Carbon $date): bool
    {
        // Check if it's a weekend (Friday/Saturday)
        $dayOfWeek = $date->dayOfWeek;
        if (in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
            return false;
        }

        // Check holidays table if exists
        try {
            $holidayCount = DB::table('holidays')
                ->whereDate('holiday_date', $date->format('Y-m-d'))
                ->count();
            if ($holidayCount > 0) {
                return false;
            }
        } catch (\Exception $e) {
            // holidays table may not exist
        }

        return true;
    }
}
