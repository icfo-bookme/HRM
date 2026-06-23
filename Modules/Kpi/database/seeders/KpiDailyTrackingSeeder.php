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

        // Generate daily tracking for last 30 days
        foreach ($employees as $employee) {
            for ($dayOffset = 0; $dayOffset < 30; $dayOffset++) {
                $date = $now->copy()->subDays($dayOffset);

                // Skip weekends (Friday/Saturday)
                $dayOfWeek = $date->dayOfWeek;
                if (in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
                    continue;
                }

                $isWorkingDay = true;
                $isPresent = rand(0, 10) > 1; // 90% chance present
                $isLate = $isPresent ? (rand(0, 10) > 7) : false; // 30% of present days are late

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

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($trackings, 100);
        foreach ($chunks as $chunk) {
            DB::table('kpi_daily_tracking')->insert($chunk);
        }

        $this->command->info('✓ KPI daily tracking seeded for ' . count($employees) . ' employees (30 days)');
    }
}