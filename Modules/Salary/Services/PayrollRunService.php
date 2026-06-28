<?php

namespace Modules\Salary\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Salary\Models\PayrollRun;
use Modules\Salary\Models\EmployeeSalaryStructure;
use Modules\Salary\Models\SalaryComponent;
use Modules\Salary\Models\PayrollRunDetail;

use Modules\Employee\Models\Employee;
use Modules\Attendance\Models\Attendance;
use Modules\Loan\Services\LoanService;
use Yajra\DataTables\DataTables;

class PayrollRunService
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Get payroll runs data for DataTable
     */
    public function getPayrollRunDataTable(Request $request)
    {
        $query = PayrollRun::query()->select(
            'payroll_runs.id',
            'payroll_runs.run_label',
            'payroll_runs.fiscal_year_id',
            'payroll_runs.run_month',
            'payroll_runs.run_type',
            'payroll_runs.total_employees',
            'payroll_runs.total_gross',
            'payroll_runs.total_net',
            'payroll_runs.total_deductions',
            'payroll_runs.status',
            'payroll_runs.created_at'
        )->latest();

        if ($request->filled('status')) {
            $query->where('payroll_runs.status', $request->status);
        }

        if ($request->filled('run_type')) {
            $query->where('payroll_runs.run_type', $request->run_type);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('fiscal_year_id', function ($run) {
                return $run->fiscalYear?->label ?? 'N/A';
            })
            ->editColumn('run_month', function (PayrollRun $run) {
                return $run->run_month->format('F Y');
            })
            ->editColumn('run_type', function (PayrollRun $run) {
                $colors = [
                    'Regular'     => 'bg-blue-100 text-blue-700',
                    'Bonus'       => 'bg-green-100 text-green-700',
                    'Advance'     => 'bg-yellow-100 text-yellow-700',
                    'Adjustment'  => 'bg-purple-100 text-purple-700',
                ];
                $color = $colors[$run->run_type] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $run->run_type . '</span>';
            })
            ->editColumn('total_gross', fn($run) => number_format($run->total_gross, 2))
            ->editColumn('total_net', fn($run) => number_format($run->total_net, 2))
            ->editColumn('total_deductions', fn($run) => number_format($run->total_deductions, 2))
            ->editColumn('status', function (PayrollRun $run) {
                $colors = [
                    'Calculated' => 'bg-yellow-100 text-yellow-700',
                    'Approved'   => 'bg-green-100 text-green-700',
                    'Locked'     => 'bg-purple-100 text-purple-700',
                    'Cancelled'  => 'bg-red-100 text-red-700',
                ];
                $color = $colors[$run->status] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $run->status . '</span>';
            })
            ->editColumn('created_at', fn(PayrollRun $run) => $run->created_at->format('d M Y H:i'))
            ->addColumn('action', function (PayrollRun $run) {
                $btn = '<a href="' . route('payroll-runs.show-generated', $run->id) . '" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 mr-1" title="View Details"><i class="fas fa-eye"></i> View</a>';
                if ($run->status !== 'Locked') {
                    $btn .= '<button onclick="payrollRunDelete(' . $run->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 ml-1" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['run_type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Get the monthly working days (excluding weekly off days and holidays)
     * Uses the employee's shift weekly_off_days configuration.
     * Example weekly_off_days: [0] = Sunday off, [5,6] = Friday+Saturday off, etc.
     * Carbon dayOfWeek: 0=Sunday, 1=Monday, ..., 6=Saturday
     * If no weekly off days provided, defaults to Friday+Saturday off.
     */
    private function getMonthlyWorkingDays(string $yearMonth, ?array $weeklyOffDays = null): array
    {
        $startOfMonth = Carbon::parse($yearMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($yearMonth)->endOfMonth();
        $workingDays = 0;
        $totalDays = 0;
        $dates = [];

        // Default to Friday+Saturday if no weekly off days configured
        $offDays = $weeklyOffDays ?? [Carbon::FRIDAY, Carbon::SATURDAY];

        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $totalDays++;
            $isWeekend = in_array($current->dayOfWeek, $offDays);
            if (!$isWeekend) {
                $workingDays++;
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return [
            'total_days' => $totalDays,
            'working_days' => $workingDays,
            'dates' => $dates,
        ];
    }

    /**
     * Calculate attendance-based adjustments for an employee in a given month
     */
    private function calculateAttendanceAdjustments(Employee $employee, string $runMonth): array
    {
        $startOfMonth = Carbon::parse($runMonth)->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::parse($runMonth)->endOfMonth()->format('Y-m-d');

        // Get attendance records for the month as a plain array keyed by Y-m-d date
        $attendances = [];
        Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->select('*')
            ->selectRaw("DATE_FORMAT(attendance_date, '%Y-%m-%d') as date_key")
            ->get()
            ->each(function ($att) use (&$attendances) {
                $attendances[$att->date_key] = $att;
            });

        // Get employee's weekend days from employee_weekends table
        $weeklyOffDays = $employee->weekend?->weekend_days;
        $monthDays = $this->getMonthlyWorkingDays($runMonth, $weeklyOffDays);

        // Get per-employee attendance rules (falls back to salary grade)
        $rule = $employee->attendanceRule;
        $salaryGrade = $employee->salaryGrade;

        // Counters
        $totalLateDays = 0;       // Number of days employee was late (for per-day deduction)
        $totalLateMinutes = 0;    // Kept for reference
        $totalOvertimeMinutes = 0;
        $halfDayCount = 0;
        $absentCount = 0;
        $presentCount = 0;

        foreach ($monthDays['dates'] as $date) {
            // Check if attendance exists for this date using the raw date_key
            $att = null;
            if (isset($attendances[$date])) {
                $att = $attendances[$date];
            }

            if ($att === null) {
                // No attendance record - count as absent
                $absentCount++;
                continue;
            }

            switch ($att->attendance_status) {
                case 'Present':
                    $presentCount++;
                    // Late counting - check per-employee rules first
                    $lateEnabled = $rule ? $rule->enable_late_deduction : $employee->count_late_for_payroll;
                    if ($lateEnabled && $att->is_late && $att->late_minutes > 0) {
                        $graceMinutes = $rule?->late_grace_minutes ?? 0;
                        $lateMins = max(0, $att->late_minutes - $graceMinutes);
                        $totalLateMinutes += $lateMins;
                        // Each late day counted as 1 for per-day deduction
                        $totalLateDays++;
                    }
                    // Overtime counting - check per-employee rules first
                    $otEnabled = $rule ? $rule->enable_overtime : $employee->count_overtime_for_payroll;
                    if ($otEnabled && $att->overtime_minutes > 0) {
                        $totalOvertimeMinutes += $att->overtime_minutes;
                    }
                    break;

                case 'Half Day':
                    $hdEnabled = $rule ? $rule->enable_half_day_deduction : true;
                    if ($hdEnabled) {
                        $halfDayCount++;
                    }
                    break;

                case 'Absent':
                    $absEnabled = $rule ? $rule->enable_absent_deduction : true;
                    if ($absEnabled) {
                        $absentCount++;
                    }
                    break;

                case 'On Leave':
                    // On leave - no deduction, just count as present for working days
                    $presentCount++;
                    break;

                case 'Holiday':
                case 'Weekend':
                    // Already excluded - no action needed
                    break;

                default:
                    $presentCount++;
                    break;
            }
        }

        // ====== Calculate Late Deduction Amount ======
        $lateDeduction = 0;
        $lateDeductionType = 'none';
        $lateDeductionRate = 0;

        if ($rule) {
            if ($rule->enable_late_deduction) {
                $lateDeductionType = $rule->late_deduction_type;
                $lateDeductionRate = $rule->late_deduction_per_minute;
            }
        } elseif ($salaryGrade && $salaryGrade->deduct_late_for_payroll) {
            $lateDeductionType = 'per_minute';
            $lateDeductionRate = $salaryGrade->late_deduction_per_minute;
        }

        // Calculate based on type
        if ($lateDeductionType === 'per_minute' && $lateDeductionRate > 0 && $totalLateMinutes > 0) {
            $lateDeduction = $totalLateMinutes * $lateDeductionRate;
        } elseif ($lateDeductionType === 'half_day' && $totalLateDays > 0) {
            // Half day: each late day = half day salary deducted (calculated later with daily rate)
            $halfDayCount += $totalLateDays;
        } elseif ($lateDeductionType === 'full_day' && $totalLateDays > 0) {
            // Full day: each late day = full absent day
            $absentCount += $totalLateDays;
        }

        // ====== Half Day & Absent Settings ======
        $halfDayDeductionPercent = 50;
        $absentDeductionDays = 1;
        $payOvertime = false;

        if ($rule) {
            $halfDayDeductionPercent = $rule->enable_half_day_deduction ? $rule->half_day_deduction_percent : 0;
            $absentDeductionDays = $rule->enable_absent_deduction ? $rule->absent_deduction_days : 0;
            $payOvertime = $rule->enable_overtime;
        } else {
            $halfDayDeductionPercent = $salaryGrade?->half_day_deduction_percent ?? 50;
            $absentDeductionDays = $salaryGrade?->absent_deduction_days ?? 1;
            $payOvertime = $employee->count_overtime_for_payroll && $salaryGrade?->pay_overtime_for_payroll;
        }

        return [
            'total_late_days' => $totalLateDays,
            'total_late_minutes' => $totalLateMinutes,
            'total_overtime_minutes' => $totalOvertimeMinutes,
            'half_day_count' => $halfDayCount,
            'absent_count' => $absentCount,
            'present_count' => $presentCount,
            'working_days' => $monthDays['working_days'],
            'late_deduction' => $lateDeduction,
            'late_deduction_type' => $lateDeductionType,
            'half_day_deduction_percent' => $halfDayDeductionPercent,
            'absent_deduction_days' => $absentDeductionDays,
            'pay_overtime' => $payOvertime,
            // Per-employee overtime rate info
            'overtime_rate_per_hour' => $rule?->overtime_rate_per_hour ?? 0,
            'overtime_multiplier' => $rule?->overtime_multiplier ?? 1.5,
        ];
    }

    /**
     * Calculate per-day salary for an employee
     */
    private function calculateDailyRate(float $basicSalary, int $workingDays): float
    {
        if ($workingDays <= 0) return 0;
        return $basicSalary / $workingDays;
    }

    public function generatePayroll(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $runMonth = $data['run_month'];
                $employees = Employee::with(['personalInfo', 'salaryGrade', 'shift', 'weekend', 'attendanceRule'])->get();

                if ($employees->isEmpty()) {
                    return ['status' => 'error', 'message' => 'No employees found.'];
                }

                $totalGross = 0;
                $totalDeductions = 0;
                $totalLoanDeductions = 0;
                $employeeCount = 0;
                $processedEmployeeIds = [];

                foreach ($employees as $employee) {
                    $structures = EmployeeSalaryStructure::with('component')
                        ->where('employee_id', $employee->id)
                        ->where('effective_from', '<=', $runMonth)
                        ->where(function ($q) use ($runMonth) {
                            $q->whereNull('effective_to')->orWhere('effective_to', '>=', $runMonth);
                        })->get();

                    if ($structures->isEmpty()) continue;
                    $employeeCount++;
                    $processedEmployeeIds[] = $employee->id;

                    $basicSalary = 0;
                    $basicStruct = $structures->where('component.name', 'Basic Salary')->first();
                    if ($basicStruct) $basicSalary = $basicStruct->amount;

                    // Calculate attendance adjustments
                    $attendanceAdjustments = $this->calculateAttendanceAdjustments($employee, $runMonth);

                    // Daily rate for attendance-based calculations
                    $dailyRate = $this->calculateDailyRate($basicSalary, $attendanceAdjustments['working_days']);

                    $earnings = $structures->filter(fn($s) => $s->component && $s->component->type === 'Earning');
                    $deductions = $structures->filter(fn($s) => $s->component && $s->component->type === 'Deduction');
                    $totalEarnings = 0;
                    $empDeductions = 0;

                    // -- Process Earning Components --
                    foreach ($earnings as $struct) {
                        $totalEarnings += $struct->is_percentage ? ($struct->amount / 100) * $basicSalary : $struct->amount;
                    }

                    // -- Overtime Pay (added as additional earning) --
                    if ($attendanceAdjustments['pay_overtime'] && $attendanceAdjustments['total_overtime_minutes'] > 0) {
                        $hourlyRate = $dailyRate / 8;
                        $overtimeHours = $attendanceAdjustments['total_overtime_minutes'] / 60;
                        // Use per-employee overtime rate if set, else calculate from hourly rate with multiplier
                        $otRatePerHour = $attendanceAdjustments['overtime_rate_per_hour'];
                        $otMultiplier = $attendanceAdjustments['overtime_multiplier'];
                        if ($otRatePerHour > 0) {
                            $overtimePay = $overtimeHours * $otRatePerHour;
                        } else {
                            $overtimePay = $overtimeHours * $hourlyRate * $otMultiplier;
                        }
                        $totalEarnings += $overtimePay;
                    }

                    // -- Process Deduction Components --
                    foreach ($deductions as $struct) {
                        $calcType = $struct->component->calculation_type;
                        if ($struct->is_percentage || $calcType === 'Percentage of Basic') {
                            $empDeductions += ($struct->amount / 100) * $basicSalary;
                        } elseif ($calcType === 'Percentage of Gross') {
                            $empDeductions += ($struct->amount / 100) * $totalEarnings;
                        } else {
                            $empDeductions += $struct->amount;
                        }
                    }

                    // -- Attendance-Based Deductions --

                    // Late deduction
                    if ($attendanceAdjustments['late_deduction'] > 0) {
                        $empDeductions += $attendanceAdjustments['late_deduction'];
                    }

                    // Half-day deduction
                    if ($attendanceAdjustments['half_day_count'] > 0) {
                        $halfDayPercent = $attendanceAdjustments['half_day_deduction_percent'] / 100;
                        $halfDayDeduction = $attendanceAdjustments['half_day_count'] * $dailyRate * $halfDayPercent;
                        $empDeductions += $halfDayDeduction;
                    }

                    // Absent deduction
                    if ($attendanceAdjustments['absent_count'] > 0) {
                        $absentDeduction = $attendanceAdjustments['absent_count'] * $dailyRate * $attendanceAdjustments['absent_deduction_days'];
                        $empDeductions += $absentDeduction;
                    }

                    // -- Loan Installment Deduction --
                    $loanDeduction = $this->loanService->getEmployeeLoanDeductions($employee->id, $runMonth);
                    if ($loanDeduction > 0) {
                        $empDeductions += $loanDeduction;
                        $totalLoanDeductions += $loanDeduction;
                    }

                    $totalGross += $totalEarnings;
                    $totalDeductions += $empDeductions;
                }

                $totalNet = $totalGross - $totalDeductions;
                $monthName = Carbon::parse($runMonth)->format('F Y');

                $data['total_employees']  = $employeeCount;
                $data['total_gross']      = round($totalGross, 2);
                $data['total_deductions'] = round($totalDeductions, 2);
                $data['total_net']        = round($totalNet, 2);
                $data['status']           = 'Calculated';
                $data['created_by']       = auth()->id();
                $data['run_label']        = $data['run_label'] ?? $monthName . ' Payroll';

                $run = PayrollRun::create($data);

                return [
                    'status'     => 'success',
                    'message'    => "Payroll generated successfully for {$employeeCount} employees. Loan deductions: " . number_format($totalLoanDeductions, 2) . ".",
                    'payroll_run' => $run->fresh()->load(['fiscalYear', 'createdBy']),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error generating payroll: ' . $e->getMessage(), 'payroll_run' => null];
        }
    }

    public function recalculatePayroll(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $run = PayrollRun::findOrFail($id);
                if ($run->status === 'Locked') {
                    return ['status' => 'error', 'message' => 'Locked payroll runs cannot be recalculated.'];
                }

                $runMonth = $run->run_month->format('Y-m-d');
                $employees = Employee::with(['personalInfo', 'salaryGrade', 'shift', 'weekend', 'attendanceRule'])->get();
                $totalGross = 0;
                $totalDeductions = 0;
                $totalLoanDeductions = 0;
                $employeeCount = 0;

                foreach ($employees as $employee) {
                    $structures = EmployeeSalaryStructure::with('component')
                        ->where('employee_id', $employee->id)
                        ->where('effective_from', '<=', $runMonth)
                        ->where(function ($q) use ($runMonth) {
                            $q->whereNull('effective_to')->orWhere('effective_to', '>=', $runMonth);
                        })->get();

                    if ($structures->isEmpty()) continue;
                    $employeeCount++;
                    $totalEarnings = 0;
                    $empDeductions = 0;

                    $basicSalary = 0;
                    $basicStruct = $structures->where('component.name', 'Basic Salary')->first();
                    if ($basicStruct) $basicSalary = $basicStruct->amount;

                    // Calculate attendance adjustments
                    $attendanceAdjustments = $this->calculateAttendanceAdjustments($employee, $runMonth);
                    $dailyRate = $this->calculateDailyRate($basicSalary, $attendanceAdjustments['working_days']);

                    $earnings = $structures->filter(fn($s) => $s->component && $s->component->type === 'Earning');
                    $deductions = $structures->filter(fn($s) => $s->component && $s->component->type === 'Deduction');

                    foreach ($earnings as $struct) {
                        $totalEarnings += $struct->is_percentage ? ($struct->amount / 100) * $basicSalary : $struct->amount;
                    }

                    // Overtime Pay
                    if ($attendanceAdjustments['pay_overtime'] && $attendanceAdjustments['total_overtime_minutes'] > 0) {
                        $hourlyRate = $dailyRate / 8;
                        $overtimeHours = $attendanceAdjustments['total_overtime_minutes'] / 60;
                        $otRatePerHour = $attendanceAdjustments['overtime_rate_per_hour'];
                        $otMultiplier = $attendanceAdjustments['overtime_multiplier'];
                        if ($otRatePerHour > 0) {
                            $overtimePay = $overtimeHours * $otRatePerHour;
                        } else {
                            $overtimePay = $overtimeHours * $hourlyRate * $otMultiplier;
                        }
                        $totalEarnings += $overtimePay;
                    }

                    foreach ($deductions as $struct) {
                        $calcType = $struct->component->calculation_type;
                        if ($struct->is_percentage || $calcType === 'Percentage of Basic') {
                            $empDeductions += ($struct->amount / 100) * $basicSalary;
                        } elseif ($calcType === 'Percentage of Gross') {
                            $empDeductions += ($struct->amount / 100) * $totalEarnings;
                        } else {
                            $empDeductions += $struct->amount;
                        }
                    }

                    // Attendance deductions
                    if ($attendanceAdjustments['late_deduction'] > 0) {
                        $empDeductions += $attendanceAdjustments['late_deduction'];
                    }
                    if ($attendanceAdjustments['half_day_count'] > 0) {
                        $halfDayPercent = $attendanceAdjustments['half_day_deduction_percent'] / 100;
                        $empDeductions += $attendanceAdjustments['half_day_count'] * $dailyRate * $halfDayPercent;
                    }
                    if ($attendanceAdjustments['absent_count'] > 0) {
                        $empDeductions += $attendanceAdjustments['absent_count'] * $dailyRate * $attendanceAdjustments['absent_deduction_days'];
                    }

                    // -- Loan Installment Deduction --
                    // For recalculation, look for installments already linked to this run
                    $loanDeduction = $this->loanService->getEmployeeLoanDeductions($employee->id, $runMonth);

                    $empDeductions += $loanDeduction;
                    $totalLoanDeductions += $loanDeduction;

                    $totalGross += $totalEarnings;
                    $totalDeductions += $empDeductions;
                }

                $run->update([
                    'total_employees'  => $employeeCount,
                    'total_gross'      => round($totalGross, 2),
                    'total_deductions' => round($totalDeductions, 2),
                    'total_net'        => round($totalGross - $totalDeductions, 2),
                    'status'           => 'Calculated',
                ]);

                return [
                    'status'     => 'success',
                    'message'    => "Payroll recalculated successfully for {$employeeCount} employees. Loan deductions: " . number_format($totalLoanDeductions, 2) . ".",
                    'payroll_run' => $run->fresh()->load(['fiscalYear', 'createdBy']),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error recalculating payroll: ' . $e->getMessage(), 'payroll_run' => null];
        }
    }

    public function approvePayroll(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $run = PayrollRun::findOrFail($id);
                if ($run->status === 'Locked') {
                    return ['status' => 'error', 'message' => 'Locked payroll runs cannot be approved.'];
                }
                if ($run->status === 'Approved') {
                    return ['status' => 'error', 'message' => 'Payroll run is already approved.'];
                }
                $run->update(['status' => 'Approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
                return ['status' => 'success', 'message' => 'Payroll run approved successfully.', 'payroll_run' => $run->fresh()];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error approving payroll: ' . $e->getMessage()];
        }
    }

    public function lockPayroll(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $run = PayrollRun::findOrFail($id);
                if ($run->status === 'Locked') {
                    return ['status' => 'error', 'message' => 'Payroll run is already locked.'];
                }
                if ($run->status !== 'Approved') {
                    return ['status' => 'error', 'message' => 'Payroll run must be approved before locking.'];
                }
                $run->update(['status' => 'Locked', 'disbursed_by' => auth()->id(), 'disbursed_at' => now()]);
                // -- Freeze a snapshot of all employee calculation data for this payroll run --
                $this->snapshotPayrollRunDetails($run);
                $employeeIds = PayrollRunDetail::where('payroll_run_id', $run->id)->pluck('employee_id')->toArray();
                $this->loanService->markInstallmentsInProgressForPayrollRun(
                    $run->id,
                    $run->run_month->format('Y-m-d'),
                    $employeeIds
                );


                return ['status' => 'success', 'message' => 'Payroll run locked successfully.', 'payroll_run' => $run->fresh()];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error locking payroll: ' . $e->getMessage()];
        }
    }

    public function getPayrollRunById(int $id): array
    {
        try {
            $run = PayrollRun::with(['fiscalYear', 'approvedBy', 'createdBy', 'disbursedBy'])->findOrFail($id);
            return ['status' => 'success', 'payroll_run' => $run];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Payroll run not found.', 'payroll_run' => null];
        }
    }

    public function deletePayrollRun(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $run = PayrollRun::findOrFail($id);
                if ($run->status === 'Locked') {
                    return ['status' => 'error', 'message' => 'Locked payroll runs cannot be deleted.'];
                }

                // Revert loan installments before deleting the run
                $this->loanService->revertInstallmentsForPayrollRun($run->id);

                $run->delete();
                return ['status' => 'success', 'message' => 'Payroll run deleted successfully.'];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error deleting payroll run: ' . $e->getMessage()];
        }
    }

    /**
     * Preview payroll with per-employee component breakdown including attendance
     */
    public function previewPayroll(string $runMonth): array
    {
        $employees = Employee::with(['personalInfo', 'salaryGrade', 'shift', 'weekend', 'attendanceRule'])->get();
        $previewData = [];

        foreach ($employees as $employee) {
            $structures = EmployeeSalaryStructure::with('component')
                ->where('employee_id', $employee->id)
                ->where('effective_from', '<=', $runMonth)
                ->where(function ($q) use ($runMonth) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', $runMonth);
                })->get();

            if ($structures->isEmpty()) continue;

            // Attendance adjustments
            $attendanceAdjustments = $this->calculateAttendanceAdjustments($employee, $runMonth);

            $basicSalary = 0;
            $basicStruct = $structures->where('component.name', 'Basic Salary')->first();
            if ($basicStruct) $basicSalary = $basicStruct->amount;

            $dailyRate = $this->calculateDailyRate($basicSalary, $attendanceAdjustments['working_days']);

            $earnings = $structures->filter(fn($s) => $s->component && $s->component->type === 'Earning');
            $deductions = $structures->filter(fn($s) => $s->component && $s->component->type === 'Deduction');

            $componentDetails = [];
            $totalEarnings = 0;
            $totalDeductions = 0;

            // Process earnings
            foreach ($earnings as $struct) {
                $calcDesc = '';
                $calculatedAmount = 0;
                if ($struct->is_percentage) {
                    $calculatedAmount = ($struct->amount / 100) * $basicSalary;
                    $calcDesc = $struct->amount . '% of Basic (' . number_format($basicSalary, 2) . ')';
                } else {
                    $calculatedAmount = $struct->amount;
                    $calcDesc = 'Fixed amount';
                }
                $totalEarnings += $calculatedAmount;

                $componentDetails[] = [
                    'component_id'     => $struct->component_id,
                    'name'             => $struct->component->name,
                    'type'             => 'Earning',
                    'calculation_type' => $struct->component->calculation_type,
                    'value'            => $struct->amount,
                    'is_pct'           => $struct->is_percentage,
                    'calc'             => $calcDesc,
                    'amount'           => round($calculatedAmount, 2),
                ];
            }

            // Overtime Pay as additional earning component
            if ($attendanceAdjustments['pay_overtime'] && $attendanceAdjustments['total_overtime_minutes'] > 0) {
                $hourlyRate = $dailyRate / 8;
                $overtimeHours = $attendanceAdjustments['total_overtime_minutes'] / 60;
                $otRatePerHour = $attendanceAdjustments['overtime_rate_per_hour'];
                $otMultiplier = $attendanceAdjustments['overtime_multiplier'];
                if ($otRatePerHour > 0) {
                    $overtimePay = $overtimeHours * $otRatePerHour;
                    $calcDesc = $attendanceAdjustments['total_overtime_minutes'] . ' min overtime @ ' . number_format($otRatePerHour, 2) . '/hr';
                } else {
                    $overtimePay = $overtimeHours * $hourlyRate * $otMultiplier;
                    $calcDesc = $attendanceAdjustments['total_overtime_minutes'] . ' min overtime @ ' . number_format($hourlyRate * $otMultiplier, 2) . '/hr (' . $otMultiplier . 'x)';
                }
                $totalEarnings += $overtimePay;

                $componentDetails[] = [
                    'name'     => 'Overtime Pay',
                    'type'     => 'Earning',
                    'value'    => $overtimePay,
                    'is_pct'   => false,
                    'calc'     => $calcDesc,
                    'amount'   => round($overtimePay, 2),
                ];
            }

            // Process deductions
            foreach ($deductions as $struct) {
                $calcDesc = '';
                $calculatedAmount = 0;
                $calcType = $struct->component->calculation_type;

                if ($struct->is_percentage || $calcType === 'Percentage of Basic') {
                    $calculatedAmount = ($struct->amount / 100) * $basicSalary;
                    $calcDesc = $struct->amount . '% of Basic (' . number_format($basicSalary, 2) . ')';
                } elseif ($calcType === 'Percentage of Gross') {
                    $calculatedAmount = ($struct->amount / 100) * $totalEarnings;
                    $calcDesc = $struct->amount . '% of Gross (' . number_format($totalEarnings, 2) . ')';
                } else {
                    $calculatedAmount = $struct->amount;
                    $calcDesc = 'Fixed amount';
                }
                $totalDeductions += $calculatedAmount;

                $componentDetails[] = [
                    'component_id'     => $struct->component_id,
                    'name'             => $struct->component->name,
                    'type'             => 'Deduction',
                    'calculation_type' => $struct->component->calculation_type,
                    'value'            => $struct->amount,
                    'is_pct'           => $struct->is_percentage,
                    'calc'             => $calcDesc,
                    'amount'           => round($calculatedAmount, 2),
                ];
            }

            // Attendance-based deductions
            if ($attendanceAdjustments['late_deduction'] > 0) {
                $totalDeductions += $attendanceAdjustments['late_deduction'];
                $componentDetails[] = [
                    'name'     => 'Late Deduction',
                    'type'     => 'Deduction',
                    'value'    => $attendanceAdjustments['late_deduction'],
                    'is_pct'   => false,
                    'calc'     => $attendanceAdjustments['total_late_minutes'] . ' min late',
                    'amount'   => round($attendanceAdjustments['late_deduction'], 2),
                ];
            }

            if ($attendanceAdjustments['half_day_count'] > 0) {
                $halfDayPercent = $attendanceAdjustments['half_day_deduction_percent'] / 100;
                $halfDayDeduction = $attendanceAdjustments['half_day_count'] * $dailyRate * $halfDayPercent;
                $totalDeductions += $halfDayDeduction;
                $componentDetails[] = [
                    'name'     => 'Half Day Deduction',
                    'type'     => 'Deduction',
                    'value'    => $halfDayDeduction,
                    'is_pct'   => true,
                    'calc'     => $attendanceAdjustments['half_day_count'] . ' half day(s) @ ' . $attendanceAdjustments['half_day_deduction_percent'] . '%',
                    'amount'   => round($halfDayDeduction, 2),
                ];
            }

            if ($attendanceAdjustments['absent_count'] > 0) {
                $absentDeduction = $attendanceAdjustments['absent_count'] * $dailyRate * $attendanceAdjustments['absent_deduction_days'];
                $totalDeductions += $absentDeduction;
                $componentDetails[] = [
                    'name'     => 'Absent Deduction',
                    'type'     => 'Deduction',
                    'value'    => $absentDeduction,
                    'is_pct'   => false,
                    'calc'     => $attendanceAdjustments['absent_count'] . ' day(s) absent @ ' . number_format($dailyRate, 2) . '/day',
                    'amount'   => round($absentDeduction, 2),
                ];
            }

            // -- Loan Installment Deduction --
            $loanDeduction = $this->loanService->getEmployeeLoanDeductions($employee->id, $runMonth);
            if ($loanDeduction > 0) {
                $totalDeductions += $loanDeduction;
                $componentDetails[] = [
                    'name'     => 'Loan Installment',
                    'type'     => 'Deduction',
                    'value'    => $loanDeduction,
                    'is_pct'   => false,
                    'calc'     => 'Monthly installment',
                    'amount'   => round($loanDeduction, 2),
                ];
            }

            $previewData[] = [
                'employee_id'   => $employee->id,
                'employee_name' => $employee->personalInfo?->full_name ?? 'N/A',
                'employee_code' => $employee->employee_code,
                'basic_salary'  => $basicSalary,
                'gross'         => round($totalEarnings, 2),
                'deductions'    => round($totalDeductions, 2),
                'net'           => round($totalEarnings - $totalDeductions, 2),
                'components'    => $componentDetails,
                // Attendance summary
                'attendance_summary' => [
                    'present' => $attendanceAdjustments['present_count'],
                    'late_days' => $attendanceAdjustments['total_late_days'],
                    'late_minutes' => $attendanceAdjustments['total_late_minutes'],
                    'overtime_minutes' => $attendanceAdjustments['total_overtime_minutes'],
                    'half_days' => $attendanceAdjustments['half_day_count'],
                    'absent' => $attendanceAdjustments['absent_count'],
                    'working_days' => $attendanceAdjustments['working_days'],
                ],
            ];
        }

        return [
            'employees' => $previewData,
            'totals'    => [
                'count'      => count($previewData),
                'gross'      => round(array_sum(array_column($previewData, 'gross')), 2),
                'deductions' => round(array_sum(array_column($previewData, 'deductions')), 2),
                'net'        => round(array_sum(array_column($previewData, 'net')), 2),
            ]
        ];
    }

    // ========================================================================
    // Payment Methods (for payment list & marking as paid)
    // ========================================================================

    /**
     * Get payroll run details for payment list DataTable.
     * Only returns data for locked payroll runs (which have snapshots).
     */
    public function getPaymentListDataTable(Request $request)
    {
        $query = PayrollRunDetail::with(['payrollRun', 'createdBy'])
            ->select(
                'id',
                'payroll_run_id',
                'employee_name',
                'employee_code',
                'basic_salary',
                'gross',
                'deductions',
                'net',
                'payment_status',
                'created_by',
                'created_at'
            )->orderBy('id','desc');

        // Filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('payroll_run_id')) {
            $query->where('payroll_run_id', $request->payroll_run_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('payroll_run_id', function ($d) {
                return $d->payrollRun?->run_label ?? 'N/A';
            })

            ->editColumn('basic_salary', fn($d) => number_format($d->basic_salary, 2))
            ->editColumn('gross', fn($d) => number_format($d->gross, 2))
            ->editColumn('deductions', fn($d) => number_format($d->deductions, 2))
            ->editColumn('net', fn($d) => number_format($d->net, 2))
            ->editColumn('created_by', function ($d) {
                return $d->createdBy?->name ?? 'System';
            })
            ->editColumn('payment_status', function ($d) {
                if ($d->payment_status) {
                    return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700"><i class="fas fa-check-circle"></i> Paid</span>';
                }
                return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Unpaid</span>';
            })
            ->addColumn('action', function ($d) {
                if ($d->payment_status) {
                    return '<span class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-100 rounded-lg"><i class="fas fa-check"></i> Paid</span>';
                }
                return '<button onclick="markAsPaid(' . $d->id . ')" class="px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition flex items-center gap-1"><i class="fas fa-credit-card"></i> Pay</button>';
            })
            ->rawColumns(['payment_status', 'action'])
            ->make(true);
    }

    /**
     * Mark a payroll run detail as paid.
     */
    public function markDetailAsPaid(int $detailId): array
    {
        try {
            $detail = PayrollRunDetail::findOrFail($detailId);
            if ($detail->payment_status) {
                return ['status' => 'error', 'message' => 'This employee is already marked as paid.'];
            }
            $detail->update(['payment_status' => 1]);
            $this->loanService->markProgressInstallmentsPaidForEmployee($detail->employee_id, $detail->payroll_run_id);
            return ['status' => 'success', 'message' => 'Payment marked as paid successfully.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }



    // ========================================================================
    // Snapshot Helpers (used when locking / viewing locked payroll runs)
    // ========================================================================

    /**
     * Freeze the current payroll calculation data into payroll_run_details.
     * Called automatically when a payroll run is locked.
     */
    private function snapshotPayrollRunDetails(PayrollRun $run): void
    {
        // Reuse the preview logic to get current calculated data for every employee
        $preview = $this->previewPayroll($run->run_month->format('Y-m-d'));

        // Remove any previous snapshot data (e.g. if re-locked after unlock)
        PayrollRunDetail::where('payroll_run_id', $run->id)->delete();

        $now = now();
        $userId = auth()->id();
        $chunks = [];

        foreach ($preview['employees'] as $emp) {
            $chunks[] = [
                'payroll_run_id'    => $run->id,
                'employee_id'       => $emp['employee_id'],
                'employee_name'     => $emp['employee_name'],
                'employee_code'     => $emp['employee_code'],
                'basic_salary'      => $emp['basic_salary'],
                'gross'             => $emp['gross'],
                'deductions'        => $emp['deductions'],
                'net'               => $emp['net'],
                'component_details' => json_encode($emp['components']),
                'attendance_summary' => json_encode($emp['attendance_summary']),
                'payment_status'    => 0,
                'created_by'        => $userId,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        // Bulk insert for performance
        if (!empty($chunks)) {
            PayrollRunDetail::insert($chunks);
        }
    }

    /**
     * Load frozen payroll-run data from the snapshot table.
     * Called by getPayrollRunWithEmployees() when a locked run has a snapshot.
     *
     * @return array{run: PayrollRun, employees: array, totals: array}
     */
    private function getLockedPayrollRunData(PayrollRun $run): array
    {
        $details = PayrollRunDetail::where('payroll_run_id', $run->id)->get();

        $employees = [];
        $totals = [
            'count'      => 0,
            'gross'      => 0,
            'deductions' => 0,
            'net'        => 0,
        ];

        foreach ($details as $detail) {
            $employees[] = [
                'employee_id'       => $detail->employee_id,
                'employee_name'     => $detail->employee_name,
                'employee_code'     => $detail->employee_code,
                'basic_salary'      => (float) $detail->basic_salary,
                'gross'             => (float) $detail->gross,
                'deductions'        => (float) $detail->deductions,
                'net'               => (float) $detail->net,
                'components'        => $detail->component_details ?? [],
                'attendance_summary' => $detail->attendance_summary ?? [],
            ];

            $totals['count']++;
            $totals['gross'] += (float) $detail->gross;
            $totals['deductions'] += (float) $detail->deductions;
            $totals['net'] += (float) $detail->net;
        }

        $totals['gross'] = round($totals['gross'], 2);
        $totals['deductions'] = round($totals['deductions'], 2);
        $totals['net'] = round($totals['net'], 2);

        return [
            'run'       => $run,
            'employees' => $employees,
            'totals'    => $totals,
        ];
    }



    public function getPayrollRunWithEmployees(int $id): array
    {
        $run = PayrollRun::with(['fiscalYear', 'approvedBy', 'createdBy', 'disbursedBy'])->findOrFail($id);

        // If the payroll run is locked and has a snapshot, serve data from the frozen snapshot
        if ($run->hasSnapshot()) {
            return $this->getLockedPayrollRunData($run);
        }

        // Otherwise, dynamically calculate from live salary structure data
        $preview = $this->previewPayroll($run->run_month->format('Y-m-d'));
        return [
            'run'       => $run,
            'employees' => $preview['employees'],
            'totals'    => $preview['totals'],
        ];
    }
}
