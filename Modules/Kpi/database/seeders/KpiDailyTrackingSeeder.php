<?php

namespace Modules\Kpi\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class KpiDailyTrackingSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Skipping KPI daily tracking seeder.');
            return;
        }

        $trackings = [];
        $now = now();

        foreach ($employees as $employee) {
            for ($dayOffset = 0; $dayOffset < 30; $dayOffset++) {
                $date = $now->copy()->subDays($dayOffset);

                if (in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
                    continue;
                }

                $isWorkingDay = true;
                $isPresent = rand(0, 10) > 1;
                $isLate = $isPresent ? (rand(0, 10) > 7) : false;

                $presentTarget = $isWorkingDay ? 1 : 0;
                $presentObtained = ($isPresent && !$isLate) ? 1 : 0;
                $lateTarget = $isWorkingDay ? 1 : 0;
                $lateObtained = $isLate ? -2 : 0;

                $dailyTarget = $presentTarget + $lateTarget;
                $dailyObtained = $presentObtained + $lateObtained;
                $dailyPercentage = $dailyTarget > 0 ? round(($dailyObtained / $dailyTarget) * 100, 2) : 0;

                $trackings[] = [
                    'employee_id' => $employee->id,
                    'tracking_date' => $date->format('Y-m-d'),
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
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        foreach ($trackings as $tracking) {
            DB::table('kpi_daily_tracking')->updateOrInsert(
                ['employee_id' => $tracking['employee_id'], 'tracking_date' => $tracking['tracking_date']],
                $tracking
            );
        }

        $this->command->info('✓ KPI daily tracking seeded for ' . count($employees) . ' employees (30 days)');
    }
}
