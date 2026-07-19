<?php

namespace Modules\Leave\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Leave\Models\LeaveApplication;
use Modules\Employee\Models\EmployeeLeaveBalance;
use Modules\Attendance\Models\Attendance;
use Yajra\DataTables\DataTables;

class LeaveApplicationService
{
    public function getLeaveApplicationDataTable(Request $request, bool $showApproveButtons = true)
    {
        $query = LeaveApplication::with(['employee.personalInfo', 'leaveType', 'approvedBy', 'substitute'])
            ->select(
                'leave_applications.id',
                'leave_applications.employee_id',
                'leave_applications.leave_type_id',
                'leave_applications.application_no',
                'leave_applications.from_date',
                'leave_applications.to_date',
                'leave_applications.total_days',
                'leave_applications.is_half_day',
                'leave_applications.half_day_period',
                'leave_applications.status',
                'leave_applications.approved_by',
                'leave_applications.approved_at',
                'leave_applications.applied_at',
                'leave_applications.updated_at',
            )
            ->orderByDesc('leave_applications.id');

        if ($request->employee_id !== null && $request->employee_id !== '') {
            $query->where('leave_applications.employee_id', $request->employee_id);
        }

        if ($request->leave_type_id !== null && $request->leave_type_id !== '') {
            $query->where('leave_applications.leave_type_id', $request->leave_type_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('leave_applications.status', $request->status);
        }

        if ($request->from_date !== null && $request->from_date !== '') {
            $query->whereDate('leave_applications.from_date', '>=', $request->from_date);
        }

        if ($request->to_date !== null && $request->to_date !== '') {
            $query->whereDate('leave_applications.to_date', '<=', $request->to_date);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('employee', function ($query, $keyword) {
                $query->whereHas('employee.personalInfo', function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%");
                })->orWhereHas('employee', function ($q) use ($keyword) {
                    $q->where('employee_code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('leave_type', function ($query, $keyword) {
                $query->whereHas('leaveType', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('application_no', function ($app) {
                return '<span class="font-mono text-xs text-slate-500">' . e($app->application_no ?? '—') . '</span>';
            })
            ->editColumn('employee', function ($app) {
                $emp = $app->employee;
                if (!$emp) return 'N/A';
                $name = $emp->full_name ?: $emp->employee_code;
                $code = $emp->employee_code;
                return '<div class="text-sm font-medium text-slate-800">' . e($name) . '</div>' .
                    '<div class="text-xs text-slate-400">' . e($code) . '</div>';
            })
            ->editColumn('leave_type', function ($app) {
                return $app->leaveType?->name ?? 'N/A';
            })
            ->editColumn('from_date', function ($app) {
                return $app->from_date?->format('d M Y') ?? '—';
            })
            ->editColumn('to_date', function ($app) {
                return $app->to_date?->format('d M Y') ?? '—';
            })
            ->editColumn('total_days', function ($app) {
                $half = $app->is_half_day ? ' <span class="text-xs text-amber-500">(Half)</span>' : '';
                return '<span class="font-semibold">' . number_format($app->total_days, 1) . '</span>' . $half;
            })
            ->editColumn('status', function ($app) {
                return $app->status_badge;
            })
            ->editColumn('applied_at', function ($app) {
                return $app->applied_at?->format('d M Y H:i') ?? '—';
            })
            ->addColumn('action', function ($app) use ($showApproveButtons) {

                $buttons = view('components.action-buttons', [
                    'id'     => $app->id,
                    'edit'   => 'leaveApplicationEdit',
                    'delete' => 'leaveApplicationDelete',
                ])->render();

                // Only show approve/disapprove buttons for admin view
                if ($showApproveButtons) {
                    if ($app->status === LeaveApplication::STATUS_APPROVED) {
                        $buttons .= '
        <button
            type="button"
            class="bg-orange-600 text-white rounded-md mt-1 p-1"
            onclick="disapproveLeave(' . $app->id . ')">
            <i class="fa fa-undo"></i> Disapprove
        </button>
    ';
                    } else {
                        $buttons .= '
        <button
            type="button"
            class="bg-green-700 text-white rounded-md mt-1 p-1"
            onclick="approveLeave(' . $app->id . ')">
            <i class="fa fa-check"></i> Approve
        </button>
    ';
                    }
                }

                return $buttons;
            })
            ->rawColumns(['application_no', 'employee', 'total_days', 'status', 'action'])
            ->make(true);
    }

    public function saveLeaveApplication(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $applicationId = $data['application_id'] ?? null;

                // Auto-generate application number for new records
                if (!$applicationId && empty($data['application_no'])) {
                    $data['application_no'] = LeaveApplication::generateApplicationNo();
                }

                if ($applicationId) {
                    $application = LeaveApplication::findOrFail($applicationId);
                    $application->update($data);
                    $message = 'Leave application updated successfully.';
                    $status  = 'success';
                } else {
                    $application = LeaveApplication::create($data);
                    $message = 'Leave application submitted successfully.';
                    $status  = 'success';

                    // Check balance availability for the applied leave type
                    if (!empty($data['leave_type_id']) && !empty($data['employee_id'])) {
                        $leaveType = \Modules\Leave\Models\LeaveType::find($data['leave_type_id']);
                        if ($leaveType && $leaveType->affects_balance) {
                            $balance = EmployeeLeaveBalance::where('employee_id', $data['employee_id'])
                                ->where('leave_type_id', $data['leave_type_id'])
                                ->first();

                            $totalDays = $data['total_days'] ?? 0;

                            if (!$balance) {
                                // No balance record exists
                                $message .= ' ⚠️ Warning: No leave balance found for "' . $leaveType->name . '". Please contact HR to set up your balance.';
                            } else {
                                $currentRemaining = $balance->opening_balance + $balance->earned_days - $balance->used_days - $balance->encashed_days - $balance->lapsed_days - $balance->pending_days;

                                if ($currentRemaining < $totalDays) {
                                    $shortage = $totalDays - max(0, $currentRemaining);
                                    $message .= ' ⚠️ Warning: Insufficient leave balance! You have only ' . max(0, $currentRemaining) . ' ' . $leaveType->name . ' day(s) remaining, but applying for ' . $totalDays . ' day(s). You are ' . $shortage . ' day(s) short.';
                                }
                            }
                        }
                    }
                }

                return [
                    'status'  => $status,
                    'message' => $message,
                    'data'    => $application->fresh()->load(['employee.personalInfo', 'leaveType', 'approvedBy', 'substitute']),
                ];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMsg = $e->getMessage();

            if (str_contains($errorMsg, 'leave_applications_application_no_unique') || str_contains($errorMsg, 'Duplicate entry')) {
                // Regenerate and retry once
                if (empty($data['application_no'])) {
                    $data['application_no'] = LeaveApplication::generateApplicationNo();
                    return $this->saveLeaveApplication($data);
                }

                return [
                    'status'  => 'error',
                    'message' => 'Duplicate application number. Please try again.',
                    'data'    => null,
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Error saving leave application: ' . $e->getMessage(),
                'data'    => null,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving leave application: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function getLeaveApplicationById(int $id): array
    {
        try {
            $application = LeaveApplication::with(['employee.personalInfo', 'leaveType', 'approvedBy', 'substitute'])
                ->findOrFail($id);

            return [
                'status' => 'success',
                'data'   => $application,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Leave application not found.',
                'data'    => null,
            ];
        }
    }

    public function deleteLeaveApplication(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $application = LeaveApplication::findOrFail($id);

                // Only allow deletion of Draft or Cancelled applications
                if (!in_array($application->status, [LeaveApplication::STATUS_DRAFT, LeaveApplication::STATUS_CANCELLED])) {
                    return [
                        'status'  => 'error',
                        'message' => 'Only draft or cancelled applications can be deleted.',
                    ];
                }

                $application->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Leave application deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting leave application: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update leave balance: deduct days when approved, add back when disapproved
     */
    private function updateBalance(int $employeeId, int $leaveTypeId, float $days, string $operation): ?string
    {
        $balance = EmployeeLeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$balance) {
            return null;
        }

        if ($operation === 'deduct') {
            $newUsedDays = $balance->used_days + $days;
        } else { // add back
            $newUsedDays = max(0, $balance->used_days - $days);
        }

        $balance->update(['used_days' => $newUsedDays]);

        // Calculate remaining for warning
        $newRemaining = $balance->opening_balance + $balance->earned_days - $newUsedDays - $balance->encashed_days - $balance->lapsed_days - $balance->pending_days;

        if ($operation === 'deduct' && $newRemaining < 0) {
            return "Warning: Leave balance is now negative ({$newRemaining} days). Employee has exceeded their leave allocation.";
        }

        return null;
    }

    /**
     * Create attendance records for the leave period (mark as On Leave)
     */
    private function createLeaveAttendance(int $employeeId, string $fromDate, string $toDate, bool $isHalfDay = false, ?string $halfDayPeriod = null): void
    {
        $start = \Carbon\Carbon::parse($fromDate);
        $end = \Carbon\Carbon::parse($toDate);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $attendanceDate = $date->format('Y-m-d');

            // Check if attendance record already exists for this date
            $existing = Attendance::where('employee_id', $employeeId)
                ->where('attendance_date', $attendanceDate)
                ->first();

            if (!$existing) {
                $attendanceData = [
                    'employee_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                    'attendance_status' => 'On Leave',
                    'is_absent' => false,
                    'is_late' => false,
                    'is_early_out' => false,
                    'is_holiday_work' => false,
                    'break_minutes' => 0,
                    'late_minutes' => 0,
                    'early_out_minutes' => 0,
                    'overtime_minutes' => 0,
                    'working_minutes' => 0,
                    'net_working_minutes' => 0,
                    'approval_status' => 'Approved',
                    'source' => 'Leave Auto',
                    'remarks' => 'On leave (auto-generated)',
                ];

                // For half-day leaves, set check-in/out based on period
                if ($isHalfDay) {
                    $attendanceData['attendance_status'] = 'Half Day Leave';
                    if ($halfDayPeriod === 'first_half') {
                        $attendanceData['check_out_at'] = now()->setTime(13, 0, 0);
                        $attendanceData['last_out_at'] = now()->setTime(13, 0, 0);
                    } elseif ($halfDayPeriod === 'second_half') {
                        $attendanceData['check_in_at'] = now()->setTime(14, 0, 0);
                        $attendanceData['first_in_at'] = now()->setTime(14, 0, 0);
                    }
                }

                Attendance::create($attendanceData);
            }
        }
    }

    /**
     * Delete attendance records when leave is disapproved
     */
    private function deleteLeaveAttendance(int $employeeId, string $fromDate, string $toDate): void
    {
        $start = \Carbon\Carbon::parse($fromDate);
        $end = \Carbon\Carbon::parse($toDate);

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $attendanceDate = $date->format('Y-m-d');

            Attendance::where('employee_id', $employeeId)
                ->where('attendance_date', $attendanceDate)
                ->where('source', 'Leave Auto')
                ->delete();
        }
    }

    /**
     * Disapprove a leave application (revert from Approved back to Pending)
     */
    public function disapprove(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $application = LeaveApplication::with(['leaveType'])->findOrFail($id);

                if ($application->status !== LeaveApplication::STATUS_APPROVED) {
                    return [
                        'status'  => 'error',
                        'message' => 'Only approved applications can be disapproved.',
                    ];
                }

                // Delete auto-generated attendance records
                $this->deleteLeaveAttendance(
                    $application->employee_id,
                    $application->from_date->format('Y-m-d'),
                    $application->to_date->format('Y-m-d')
                );

                $application->update([
                    'status'      => LeaveApplication::STATUS_PENDING,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                // Restore leave balance: add back used days
                if ($application->leaveType && $application->leaveType->affects_balance) {
                    $this->updateBalance(
                        $application->employee_id,
                        $application->leave_type_id,
                        $application->total_days,
                        'add_back'
                    );
                }

                return [
                    'status'  => 'success',
                    'message' => 'Leave application has been disapproved and reverted to Pending.',
                    'data'    => $application->fresh()->load(['employee.personalInfo', 'leaveType']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error disapproving leave: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check leave balance before approving (returns warning without approving)
     */
    public function checkApprovalBalance(int $id): array
    {
        try {
            $application = LeaveApplication::with(['leaveType', 'employee.personalInfo'])->findOrFail($id);

            if ($application->status !== LeaveApplication::STATUS_PENDING) {
                return [
                    'can_approve' => true,
                    'warning' => null,
                ];
            }

            $warning = null;
            if ($application->leaveType && $application->leaveType->affects_balance) {
                $balance = EmployeeLeaveBalance::where('employee_id', $application->employee_id)
                    ->where('leave_type_id', $application->leave_type_id)
                    ->first();

                $empName = $application->employee?->full_name ?? 'Employee';
                $leaveTypeName = $application->leaveType->name;
                $totalDays = $application->total_days;

                if (!$balance) {
                    $warning = "⚠️ {$empName} has NO leave balance record for '{$leaveTypeName}'. If approved, a balance record will be created with {$totalDays} day(s) used (negative balance). Continue?";
                } else {
                    $currentRemaining = $balance->opening_balance + $balance->earned_days - $balance->used_days - $balance->encashed_days - $balance->lapsed_days - $balance->pending_days;

                    if ($currentRemaining < $totalDays) {
                        $shortage = $totalDays - max(0, $currentRemaining);
                        $warning = "⚠️ {$empName} has insufficient '{$leaveTypeName}' balance! Only " . max(0, $currentRemaining) . " day(s) remaining, applying for {$totalDays} day(s). Balance will go negative by {$shortage} day(s). Continue with approval?";
                    }
                }
            }

            return [
                'can_approve' => true,
                'warning' => $warning,
            ];
        } catch (\Exception $e) {
            return [
                'can_approve' => false,
                'warning' => 'Error checking balance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve a leave application
     */
    public function approve(int $id, int $approvedBy): array
    {
        try {
            return DB::transaction(function () use ($id, $approvedBy) {
                $application = LeaveApplication::with(['leaveType'])->findOrFail($id);

                if ($application->status !== LeaveApplication::STATUS_PENDING) {
                    return [
                        'status'  => 'error',
                        'message' => 'Only pending applications can be approved.',
                    ];
                }

                $application->update([
                    'status'      => LeaveApplication::STATUS_APPROVED,
                    'approved_by' => $approvedBy,
                    'approved_at' => now(),
                ]);

                // Deduct leave balance
                if ($application->leaveType && $application->leaveType->affects_balance) {
                    $balance = EmployeeLeaveBalance::where('employee_id', $application->employee_id)
                        ->where('leave_type_id', $application->leave_type_id)
                        ->first();

                    if (!$balance) {
                        // No balance record exists - create one
                        $fiscalYear = \Modules\Setting\Models\FiscalYear::where('is_current', true)->first();

                        $balanceData = [
                            'employee_id' => $application->employee_id,
                            'leave_type_id' => $application->leave_type_id,
                            'opening_balance' => 0,
                            'earned_days' => 0,
                            'used_days' => $application->total_days,
                            'encashed_days' => 0,
                            'lapsed_days' => 0,
                            'pending_days' => 0,
                        ];

                        if ($fiscalYear) {
                            $balanceData['fiscal_year_id'] = $fiscalYear->id;
                        }

                        EmployeeLeaveBalance::create($balanceData);
                    } else {
                        $this->updateBalance(
                            $application->employee_id,
                            $application->leave_type_id,
                            $application->total_days,
                            'deduct'
                        );
                    }
                }

                // Create attendance records for the leave period
                $this->createLeaveAttendance(
                    $application->employee_id,
                    $application->from_date->format('Y-m-d'),
                    $application->to_date->format('Y-m-d'),
                    $application->is_half_day,
                    $application->half_day_period
                );

                return [
                    'status'  => 'success',
                    'message' => 'Leave application approved successfully. Attendance marked as "On Leave" for the leave period.',
                    'data'    => $application->fresh()->load(['employee.personalInfo', 'leaveType']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error approving leave: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reject a leave application
     */
    public function reject(int $id, int $approvedBy, string $reason = null): array
    {
        try {
            return DB::transaction(function () use ($id, $approvedBy, $reason) {
                $application = LeaveApplication::findOrFail($id);

                if ($application->status !== LeaveApplication::STATUS_PENDING) {
                    return [
                        'status'  => 'error',
                        'message' => 'Only pending applications can be rejected.',
                    ];
                }

                $application->update([
                    'status'           => LeaveApplication::STATUS_REJECTED,
                    'approved_by'      => $approvedBy,
                    'rejection_reason' => $reason,
                    'approved_at'      => now(),
                ]);

                return [
                    'status'  => 'success',
                    'message' => 'Leave application rejected.',
                    'data'    => $application->fresh()->load(['employee.personalInfo', 'leaveType']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error rejecting leave: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get applications for a specific employee
     */
    public function getApplicationsByEmployee(int $employeeId)
    {
        return LeaveApplication::with(['leaveType', 'approvedBy'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('id')
            ->get();
    }
}
