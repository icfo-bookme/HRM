<?php

namespace Modules\Attendance\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class AttendanceDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Skipping attendance seeder.');
            return;
        }

        $attendances = [];
        $now = now();

        // Generate attendance for last 30 days
        foreach ($employees as $employee) {
            for ($dayOffset = 0; $dayOffset < 30; $dayOffset++) {
                $date = $now->copy()->subDays($dayOffset);
                $dayOfWeek = $date->dayOfWeek;

                // Weekend (Friday/Saturday in BD)
                if (in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY])) {
                    $attendances[] = [
                        'employee_id' => $employee->id,
                        'shift_id' => 1,
                        'attendance_date' => $date->format('Y-m-d'),
                        'attendance_status' => 'Weekend',
                        'is_late' => false,
                        'is_absent' => false,
                        'is_early_out' => false,
                        'is_holiday_work' => false,
                        'late_minutes' => 0,
                        'early_out_minutes' => 0,
                        'overtime_minutes' => 0,
                        'working_minutes' => 0,
                        'net_working_minutes' => 0,
                        'break_minutes' => 0,
                        'approval_status' => 'Approved',
                        'source' => 'System',
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                    continue;
                }

                // 85% present, 10% late, 5% absent
                $rand = rand(1, 100);
                if ($rand <= 85) {
                    $status = 'Present';
                    $isLate = false;
                    $isAbsent = false;
                    $lateMin = 0;
                    $checkIn = $date->copy()->setTime(8, rand(0, 15), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0);
                    $workingMin = 540; // 9 hours
                } elseif ($rand <= 95) {
                    $status = 'Late';
                    $isLate = true;
                    $isAbsent = false;
                    $lateMin = rand(15, 120);
                    $checkIn = $date->copy()->setTime(9, rand(0, 30), 0);
                    $checkOut = $date->copy()->setTime(17, rand(0, 15), 0);
                    $workingMin = 480 - $lateMin;
                } else {
                    $status = 'Absent';
                    $isLate = false;
                    $isAbsent = true;
                    $lateMin = 0;
                    $checkIn = null;
                    $checkOut = null;
                    $workingMin = 0;
                }

                $attendances[] = [
                    'employee_id' => $employee->id,
                    'shift_id' => 1,
                    'attendance_date' => $date->format('Y-m-d'),
                    'first_in_at' => $checkIn,
                    'last_out_at' => $checkOut,
                    'check_in_at' => $checkIn,
                    'check_out_at' => $checkOut,
                    'attendance_status' => $status,
                    'is_late' => $isLate,
                    'is_absent' => $isAbsent,
                    'is_early_out' => false,
                    'is_holiday_work' => false,
                    'late_minutes' => $lateMin,
                    'early_out_minutes' => 0,
                    'overtime_minutes' => 0,
                    'working_minutes' => $workingMin,
                    'net_working_minutes' => $workingMin,
                    'break_minutes' => 30,
                    'approval_status' => 'Approved',
                    'source' => 'Device',
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        // Insert in chunks
        $chunks = array_chunk($attendances, 100);
        foreach ($chunks as $chunk) {
            DB::table('attendance')->insert($chunk);
        }

        $this->command->info('✓ Attendance seeded for ' . count($employees) . ' employees (30 days)');
    }
}