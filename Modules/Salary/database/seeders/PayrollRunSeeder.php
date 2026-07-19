<?php

namespace Modules\Salary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollRunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fiscalYears = DB::table('fiscal_years')->select('id', 'start_date', 'end_date', 'label')->get();
        $user = DB::table('users')->first();
        $userId = $user ? $user->id : null;

        if ($fiscalYears->isEmpty()) {
            $this->command->info('No fiscal years found. Skipping PayrollRunSeeder.');
            return;
        }

        $now = now();
        $payrollRuns = [];

        foreach ($fiscalYears as $fy) {
            $fyStart = \Carbon\Carbon::parse($fy->start_date);
            $fyEnd = \Carbon\Carbon::parse($fy->end_date);
            $currentMonth = $now->startOfMonth();

            // Generate monthly runs for each fiscal year (up to current month)
            $monthCursor = $fyStart->copy()->startOfMonth();
            while ($monthCursor <= $fyEnd && $monthCursor <= $currentMonth) {
                $monthLabel = $monthCursor->format('F Y');
                $nextMonth = $monthCursor->copy()->addMonth();

                // Skip future months beyond current
                if ($monthCursor > $currentMonth) {
                    break;
                }

                // Determine status based on how far back the month is
                $monthsAgo = $currentMonth->diffInMonths($monthCursor);
                $status = $this->getStatusForMonth($monthsAgo);

                // Calculate realistic totals
                $employeeCount = 18;
                $avgGrossPerEmployee = $this->getAvgGrossForMonth($monthCursor->month);
                $totalGross = $employeeCount * $avgGrossPerEmployee;
                $totalDeductions = $totalGross * 0.18; // ~18% deductions
                $totalNet = $totalGross - $totalDeductions;

                $payrollRuns[] = [
                    'fiscal_year_id'  => $fy->id,
                    'run_month'       => $monthCursor->format('Y-m-d'),
                    'run_label'       => "{$monthLabel} - {$fy->label}",
                    'run_type'        => 'Regular',
                    'total_employees' => $employeeCount,
                    'total_gross'     => $totalGross,
                    'total_net'       => $totalNet,
                    'total_deductions' => $totalDeductions,
                    'status'          => $status,
                    'approved_by'     => in_array($status, ['Approved', 'Disbursed', 'Locked']) ? $userId : null,
                    'approved_at'     => in_array($status, ['Approved', 'Disbursed', 'Locked']) ? $monthCursor->copy()->addDays(25)->format('Y-m-d H:i:s') : null,
                    'disbursed_by'    => in_array($status, ['Disbursed', 'Locked']) ? $userId : null,
                    'disbursed_at'    => in_array($status, ['Disbursed', 'Locked']) ? $monthCursor->copy()->addDays(28)->format('Y-m-d H:i:s') : null,
                    'notes'           => $this->getNotesForMonth($monthCursor, $status),
                    'created_by'      => $userId,
                    'created_at'      => $monthCursor->copy()->subDays(5)->format('Y-m-d H:i:s'),
                    'updated_at'      => $now->format('Y-m-d H:i:s'),
                ];

                $monthCursor = $nextMonth;
            }
        }

        if (!empty($payrollRuns)) {
            foreach ($payrollRuns as $run) {
                DB::table('payroll_runs')->updateOrInsert(
                    [
                        'run_month' => $run['run_month'],
                        'run_type'  => $run['run_type'],
                    ],
                    $run
                );
            }
            $this->command->info(count($payrollRuns) . ' payroll runs created successfully.');
        } else {
            $this->command->info('No payroll runs to seed.');
        }
    }

    /**
     * Determine payroll status based on how many months ago.
     */
    private function getStatusForMonth(int $monthsAgo): string
    {
        if ($monthsAgo >= 3) return 'Locked';
        if ($monthsAgo === 2) return 'Disbursed';
        if ($monthsAgo === 1) return 'Approved';
        if ($monthsAgo === 0) return 'Calculated';
        return 'Draft';
    }

    /**
     * Get average gross salary per employee for a given month.
     * Slight variations to make data realistic.
     */
    private function getAvgGrossForMonth(int $month): float
    {
        $baseGross = 45000;
        $seasonalMultiplier = match ($month) {
            12              => 1.3,  // December (holiday bonus season)
            6, 7            => 1.1,  // Mid-year adjustments
            1               => 1.15, // New year increments
            default         => 1.0,
        };
        return round($baseGross * $seasonalMultiplier, 2);
    }

    /**
     * Generate realistic notes for payroll runs.
     */
    private function getNotesForMonth(\Carbon\Carbon $month, string $status): ?string
    {
        $monthNum = $month->month;
        $notes = [
            'Regular monthly payroll processed.',
            'Includes annual increment adjustments.',
            'Holiday bonus included for this month.',
            'Mid-year salary revision applied.',
            'Standard payroll run with no special adjustments.',
        ];

        if ($monthNum === 12) {
            return 'December payroll - includes festival bonus and annual performance payouts.';
        }
        if ($monthNum === 7) {
            return 'July payroll - new fiscal year start with revised salary structures.';
        }
        if ($monthNum === 1) {
            return 'January payroll - annual increment cycle applied.';
        }

        return $notes[array_rand($notes)];
    }
}