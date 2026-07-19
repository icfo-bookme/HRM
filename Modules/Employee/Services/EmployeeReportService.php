<?php

namespace Modules\Employee\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Attendance\Models\Attendance;
use Modules\Department\Models\Department;
use Modules\Designation\Models\Designation;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeePersonalInfo;
use Modules\Kpi\Models\KpiMonthlyScore;
use Modules\Loan\Models\Loan;
use Modules\Salary\Models\EmployeeSalaryStructure;
use Modules\Salary\Models\SalaryComponent;

class EmployeeReportService
{
    /**
     * Search employees by name/code with optional department and designation filters.
     */
    public function searchEmployee(Request $request): array
    {
        $keyword = $request->input('keyword');
        $departmentId = $request->input('department_id');
        $designationId = $request->input('designation_id');

        $query = Employee::with([
            'personalInfo',
            'department',
            'designation',
            'banking',
        ])->whereNull('deleted_at');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('employee_code', 'like', "%{$keyword}%")
                  ->orWhereHas('personalInfo', function ($pq) use ($keyword) {
                      $pq->where('first_name', 'like', "%{$keyword}%")
                         ->orWhere('last_name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($designationId) {
            $query->where('designation_id', $designationId);
        }

        $employees = $query->limit(50)->get();

            $results = $employees->map(function ($employee) {
            $personalInfo = $employee->personalInfo;
            $banking = $employee->banking->first();

            // Get total income from payroll if available
            $totalIncome = 0;
            try {
                if (Schema::hasTable('payroll_run_details')) {
                    $totalIncome = (float) DB::table('payroll_run_details')
                        ->join('payroll_runs', 'payroll_runs.id', '=', 'payroll_run_details.payroll_run_id')
                        ->where('payroll_run_details.employee_id', $employee->id)
                        ->whereIn('payroll_runs.status', ['Approved', 'Disbursed', 'Locked'])
                        ->sum('payroll_run_details.net');
                }
            } catch (\Exception $e) {}

            return [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
                'department' => $employee->department?->name ?? 'N/A',
                'designation' => $employee->designation?->name ?? 'N/A',
                'date_of_birth' => $personalInfo?->date_of_birth ? Carbon::parse($personalInfo->date_of_birth)->format('d M Y') : 'N/A',
                'phone' => $personalInfo?->phone ?? 'N/A',
                'email' => $personalInfo?->email ?? 'N/A',
                'profile_photo' => $personalInfo?->profile_photo ?? '',
                'gender' => $personalInfo?->gender ?? 'N/A',
                'joining_date' => $employee->joining_date ? Carbon::parse($employee->joining_date)->format('d M Y') : 'N/A',
                'bank_name' => $banking?->bank_name ?? 'N/A',
                'account_number' => $banking?->account_number ?? 'N/A',
                'payment_method' => $banking?->payment_method ?? 'N/A',
                'total_income' => number_format($totalIncome, 2),
            ];
        });

        return ['status' => true, 'employees' => $results];
    }

    /**
     * Get attendance calendar data: late days, early outs per month.
     */
    public function getAttendanceCalendar(Request $request): array
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        $events = [];
        $lateCount = 0;
        $earlyOutCount = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateStr = $date->format('Y-m-d');
            $record = $attendances->get($dateStr);

            $status = null;
            $bgColor = null;
            $tooltip = null;
            $isLate = false;
            $isEarlyOut = false;

            if ($record) {
                $isLate = $record->is_late ?? false;
                $isEarlyOut = $record->is_early_out ?? false;
                $attStatus = $record->attendance_status;

                if ($attStatus === 'Present' && $isLate) {
                    $status = 'LP';
                    $bgColor = '#ea580c';
                    $tooltip = 'Late Present - ' . ($record->late_minutes ?? 0) . ' min';
                    $lateCount++;
                } elseif ($attStatus === 'Present' && $isEarlyOut) {
                    $status = 'EL';
                    $bgColor = '#ca8a04';
                    $tooltip = 'Early Leave - ' . ($record->early_out_minutes ?? 0) . ' min';
                    $earlyOutCount++;
                } elseif ($attStatus === 'Present') {
                    $status = 'P';
                    $bgColor = '#16a34a';
                    $tooltip = 'Present';
                } elseif ($attStatus === 'Absent') {
                    $status = 'A';
                    $bgColor = '#dc2626';
                    $tooltip = 'Absent';
                } elseif ($attStatus === 'Holiday') {
                    $status = 'H';
                    $bgColor = '#2563eb';
                    $tooltip = 'Holiday';
                } elseif ($attStatus === 'On Leave') {
                    $status = 'L';
                    $bgColor = '#ca8a04';
                    $tooltip = 'On Leave';
                } elseif ($attStatus === 'Half Day') {
                    $status = 'HD';
                    $bgColor = '#ea580c';
                    $tooltip = 'Half Day';
                } else {
                    continue;
                }
            } else {
                // Check weekend from employee_weekends
                $employee = Employee::with('weekend')->find($employeeId);
                $weekendDays = $employee?->weekend?->weekend_days ?? [];
                $dayOfWeek = $date->dayOfWeek;

                if (in_array($dayOfWeek, $weekendDays)) {
                    $status = 'W';
                    $bgColor = '#9333ea';
                    $tooltip = 'Weekend';
                } else {
                    $status = 'A';
                    $bgColor = '#dc2626';
                    $tooltip = 'Absent';
                }
            }

            $events[] = [
                'title' => $status,
                'start' => $dateStr,
                'backgroundColor' => $bgColor,
                'borderColor' => $bgColor,
                'textColor' => '#ffffff',
                'display' => 'background',
            ];
        }

        return [
            'status' => true,
            'events' => $events,
            'summary' => [
                'late_count' => $lateCount,
                'early_out_count' => $earlyOutCount,
            ],
        ];
    }

    /**
     * Get overtime history for an employee (month-wise).
     */
    public function getOvertimeHistory(Request $request): array
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $records = Attendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'asc')
            ->get();

        $days = [];
        $monthTotalMinutes = 0;

        // Only show days that actually have overtime
        foreach ($records as $record) {
            $otMinutes = (int) ($record->overtime_minutes ?? 0);
            if ($otMinutes > 0) {
                $monthTotalMinutes += $otMinutes;
                $days[] = [
                    'date' => Carbon::parse($record->attendance_date)->format('d M Y'),
                    'day_num' => Carbon::parse($record->attendance_date)->day,
                    'overtime_minutes' => $otMinutes,
                    'overtime_display' => $this->formatMinutes($otMinutes),
                    'status' => 'OT',
                ];
            }
        }

        return [
            'status' => true,
            'year' => $year,
            'month' => $month,
            'month_name' => Carbon::create($year, $month, 1)->format('F Y'),
            'days' => $days,
            'month_total' => $this->formatMinutes($monthTotalMinutes),
            'month_total_minutes' => $monthTotalMinutes,
        ];
    }

    /**
     * Get salary history for an employee (structure + payroll months with month filter).
     */
    public function getSalaryHistory(Request $request): array
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', Carbon::now()->year);

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        // Get salary structure components
        $structures = EmployeeSalaryStructure::with('component')
            ->where('employee_id', $employeeId)
            ->get();

        $components = [];
        $grossSalary = 0;

        foreach ($structures as $structure) {
            $amount = (float) $structure->amount;
            $grossSalary += $amount;
            $components[] = [
                'component' => $structure->component?->name ?? 'Unknown',
                'type' => $structure->component?->type ?? 'N/A',
                'amount' => number_format($amount, 2),
                'effective_from' => $structure->effective_from ? $structure->effective_from->format('d M Y') : 'N/A',
                'effective_to' => $structure->effective_to ? $structure->effective_to->format('d M Y') : 'Ongoing',
            ];
        }

        // Try to get payroll history for the selected year
        $monthlyRecords = [];
        $totalIncome = 0;

        try {
            $hasPayrollDetails = Schema::hasTable('payroll_run_details');

            if ($hasPayrollDetails) {
                $payrollMonths = DB::table('payroll_run_details')
                    ->join('payroll_runs', 'payroll_runs.id', '=', 'payroll_run_details.payroll_run_id')
                    ->where('payroll_run_details.employee_id', $employeeId)
                    ->whereIn('payroll_runs.status', ['Approved', 'Disbursed', 'Locked'])
                    ->whereYear('payroll_runs.run_month', $year)
                    ->orderBy('payroll_runs.run_month', 'desc')
                    ->get([
                        'payroll_runs.run_month',
                        'payroll_runs.run_label',
                        'payroll_run_details.gross',
                        'payroll_run_details.deductions',
                        'payroll_run_details.net',
                    ]);

                foreach ($payrollMonths as $pm) {
                    $netPay = (float) ($pm->net ?? 0);
                    $totalIncome += $netPay;
                    $monthlyRecords[] = [
                        'month' => Carbon::parse($pm->run_month)->format('F Y'),
                        'gross_pay' => number_format($pm->gross ?? 0, 2),
                        'deductions' => number_format($pm->deductions ?? 0, 2),
                        'net_pay' => number_format($netPay, 2),
                        'status_label' => $pm->run_label,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet - just skip payroll history
        }

        return [
            'status' => true,
            'components' => $components,
            'gross_salary' => number_format($grossSalary, 2),
            'monthly_records' => $monthlyRecords,
            'total_income' => number_format($totalIncome, 2),
        ];
    }

    /**
     * Get monthly salary records for a specific month.
     */
    public function getMonthlySalary(Request $request): array
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        // Also fetch salary structure components (same as getSalaryHistory)
        $components = [];
        $grossSalary = 0;

        try {
            $hasStructure = Schema::hasTable('employee_salary_structures');

            if ($hasStructure) {
                $structures = EmployeeSalaryStructure::with('component')
                    ->where('employee_id', $employeeId)
                    ->where('status', 'Active')
                    ->get();

                foreach ($structures as $struct) {
                    $comp = $struct->component;
                    $amount = (float) $struct->amount;
                    $grossSalary += $amount;

                    $components[] = [
                        'component' => $comp?->name ?? 'N/A',
                        'type' => $comp?->type ?? 'N/A',
                        'amount' => number_format($amount, 2),
                        'effective_from' => $struct->effective_from ? Carbon::parse($struct->effective_from)->format('d M Y') : 'N/A',
                        'effective_to' => $struct->effective_to ? Carbon::parse($struct->effective_to)->format('d M Y') : 'N/A',
                    ];
                }
            }
        } catch (\Exception $e) {}

        $record = null;

        try {
            $hasPayrollDetails = Schema::hasTable('payroll_run_details');

            if ($hasPayrollDetails) {
                $record = DB::table('payroll_run_details')
                    ->join('payroll_runs', 'payroll_runs.id', '=', 'payroll_run_details.payroll_run_id')
                    ->where('payroll_run_details.employee_id', $employeeId)
                    ->whereIn('payroll_runs.status', ['Approved', 'Disbursed', 'Locked'])
                    ->whereYear('payroll_runs.run_month', $year)
                    ->whereMonth('payroll_runs.run_month', $month)
                    ->first([
                        'payroll_runs.run_month',
                        'payroll_runs.run_label',
                        'payroll_run_details.gross',
                        'payroll_run_details.deductions',
                        'payroll_run_details.net',
                    ]);
            }
        } catch (\Exception $e) {}

        if ($record) {
            return [
                'status' => true,
                'components' => $components,
                'gross_salary' => number_format($grossSalary, 2),
                'month' => Carbon::parse($record->run_month)->format('F Y'),
                'gross_pay' => number_format($record->gross ?? 0, 2),
                'deductions' => number_format($record->deductions ?? 0, 2),
                'net_pay' => number_format($record->net ?? 0, 2),
            ];
        }

        return [
            'status' => true,
            'components' => $components,
            'gross_salary' => number_format($grossSalary, 2),
            'month' => Carbon::create($year, $month, 1)->format('F Y'),
            'gross_pay' => '0.00',
            'deductions' => '0.00',
            'net_pay' => '0.00',
        ];
    }

    /**
     * Get loan history for an employee.
     */
    public function getLoanHistory(Request $request): array
    {
        $employeeId = $request->input('employee_id');

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        $loans = Loan::with(['installments'])
            ->where('employee_id', $employeeId)
            ->orderBy('application_date', 'desc')
            ->get();

        $records = [];
        $totalLoanAmount = 0;
        $totalRemaining = 0;

        foreach ($loans as $loan) {
            $paidInstallments = $loan->paid_installments ?? 0;
            $total = (float) $loan->loan_amount;
            $remaining = (float) $loan->remaining_amount;
            $totalLoanAmount += $total;
            $totalRemaining += $remaining;

            $records[] = [
                'loan_number' => $loan->loan_number,
                'loan_type' => $loan->loan_type,
                'loan_amount' => number_format($total, 2),
                'interest_rate' => $loan->interest_rate . '%',
                'total_payable' => number_format((float) $loan->total_payable, 2),
                'installment_amount' => number_format((float) $loan->installment_amount, 2),
                'total_installments' => $loan->total_installments,
                'paid_installments' => $paidInstallments,
                'remaining_amount' => number_format($remaining, 2),
                'status' => $loan->status,
                'application_date' => $loan->application_date ? $loan->application_date->format('d M Y') : 'N/A',
            ];
        }

        return [
            'status' => true,
            'records' => $records,
            'total_loan_amount' => number_format($totalLoanAmount, 2),
            'total_remaining' => number_format($totalRemaining, 2),
        ];
    }

    /**
     * Get monthly KPI history for an employee.
     */
    public function getMonthlyKpiHistory(Request $request): array
    {
        $employeeId = $request->input('employee_id');
        $year = (int) $request->input('year', Carbon::now()->year);

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        $scores = KpiMonthlyScore::where('employee_id', $employeeId)
            ->where('year', $year)
            ->orderBy('month', 'desc')
            ->get();

        $months = [];

        foreach ($scores as $score) {
            $monthName = Carbon::create($score->year, $score->month, 1)->format('F Y');
            $months[] = [
                'month' => $monthName,
                'year' => $score->year,
                'month_num' => $score->month,
                'attendance_percentage' => $score->attendance_percentage,
                'task_percentage' => $score->task_percentage,
                'behavior_score' => $score->behavior_obtained,
                'bonus_score' => $score->bonus_obtained,
                'penalty_score' => $score->penalty_obtained,
                'overall_percentage' => $score->overall_percentage,
                'rating' => $score->rating,
                'status' => $score->status,
            ];
        }

        return [
            'status' => true,
            'scores' => $months,
        ];
    }

    /**
     * Get KPI history for an employee (all-time).
     */
    public function getKpiHistory(Request $request): array
    {
        $employeeId = $request->input('employee_id');

        if (!$employeeId) {
            return ['status' => false, 'message' => 'Employee ID required.'];
        }

        $scores = KpiMonthlyScore::where('employee_id', $employeeId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $months = [];

        foreach ($scores as $score) {
            $monthName = Carbon::create($score->year, $score->month, 1)->format('F Y');
            $months[] = [
                'month' => $monthName,
                'year' => $score->year,
                'month_num' => $score->month,
                'attendance_percentage' => $score->attendance_percentage,
                'task_percentage' => $score->task_percentage,
                'behavior_score' => $score->behavior_obtained,
                'bonus_score' => $score->bonus_obtained,
                'penalty_score' => $score->penalty_obtained,
                'overall_percentage' => $score->overall_percentage,
                'rating' => $score->rating,
                'status' => $score->status,
            ];
        }

        return [
            'status' => true,
            'scores' => $months,
        ];
    }

    private function formatMinutes(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($h > 0) {
            return "{$h}h {$m}m";
        }
        return "{$m}m";
    }
}