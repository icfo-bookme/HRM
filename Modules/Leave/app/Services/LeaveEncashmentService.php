<?php

namespace Modules\Leave\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Leave\Models\LeaveEncashment;
use Modules\Employee\Models\EmployeeLeaveBalance;
use Yajra\DataTables\DataTables;

class LeaveEncashmentService
{
    /**
     * Get encashment data for DataTable
     */
    public function getLeaveEncashmentDataTable(Request $request)
    {
        $query = LeaveEncashment::with(['employee.personalInfo', 'leaveType', 'approvedBy'])
            ->select(
                'leave_encashment.id',
                'leave_encashment.employee_id',
                'leave_encashment.leave_type_id',
                'leave_encashment.encashment_date',
                'leave_encashment.days_encashed',
                'leave_encashment.amount_per_day',
                'leave_encashment.total_amount',
                'leave_encashment.payroll_run_id',
                'leave_encashment.reason',
                'leave_encashment.approved_by',
                'leave_encashment.approved_at',
                'leave_encashment.status',
                'leave_encashment.created_at',
            )
            ->orderByDesc('leave_encashment.id');

        if ($request->employee_id !== null && $request->employee_id !== '') {
            $query->where('leave_encashment.employee_id', $request->employee_id);
        }

        if ($request->leave_type_id !== null && $request->leave_type_id !== '') {
            $query->where('leave_encashment.leave_type_id', $request->leave_type_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('leave_encashment.status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('employee', function ($enc) {
                $emp = $enc->employee;
                if (!$emp) return 'N/A';
                $name = $emp->full_name ?: $emp->employee_code;
                return '<div class="text-sm font-medium text-slate-800">' . e($name) . '</div>' .
                       '<div class="text-xs text-slate-400">' . e($emp->employee_code) . '</div>';
            })
            ->editColumn('leave_type', function ($enc) {
                return $enc->leaveType?->name ?? 'N/A';
            })
            ->editColumn('encashment_date', function ($enc) {
                return $enc->encashment_date?->format('d M Y') ?? '—';
            })
            ->editColumn('days_encashed', function ($enc) {
                return number_format($enc->days_encashed, 1);
            })
            ->editColumn('amount_per_day', function ($enc) {
                return $enc->amount_per_day ? number_format($enc->amount_per_day, 2) . ' BDT' : '—';
            })
            ->editColumn('total_amount', function ($enc) {
                $total = $enc->total_amount;
                if (!$total && $enc->amount_per_day && $enc->days_encashed) {
                    $total = $enc->amount_per_day * $enc->days_encashed;
                }
                return $total
                    ? '<span class="font-semibold text-slate-800">' . number_format($total, 2) . ' BDT</span>'
                    : '—';
            })
            ->editColumn('status', function ($enc) {
                return $enc->status_badge;
            })
            ->editColumn('created_at', function ($enc) {
                return $enc->created_at?->format('d M Y H:i') ?? '—';
            })
            ->addColumn('action', function ($enc) {
                $editBtn = '<button onclick="leaveEncashmentEdit(' . $enc->id . ')"
                    class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="Edit">
                    <i class="fas fa-edit text-sm"></i>
                </button>';

                $deleteBtn = '<button onclick="leaveEncashmentDelete(' . $enc->id . ')"
                    class="p-1.5 text-slate-400 hover:text-red-600 transition" title="Delete">
                    <i class="fas fa-trash text-sm"></i>
                </button>';

                $approveBtn = '';
                if ($enc->isPending()) {
                    $approveBtn = '<button onclick="leaveEncashmentApprove(' . $enc->id . ')"
                        class="p-1.5 text-green-500 hover:text-green-700 transition" title="Approve">
                        <i class="fas fa-check-circle text-sm"></i>
                    </button>';
                }

                return '<div class="flex justify-end gap-1">' . $approveBtn . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['employee', 'total_amount', 'status', 'action'])
            ->make(true);
    }

    /**
     * Save encashment record and update leave balance
     */
    public function saveLeaveEncashment(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $encashmentId = $data['encashment_id'] ?? null;

                // Auto-calculate total_amount if not provided
                if (empty($data['total_amount']) && !empty($data['amount_per_day']) && !empty($data['days_encashed'])) {
                    $data['total_amount'] = round($data['amount_per_day'] * $data['days_encashed'], 2);
                }

                if ($encashmentId) {
                    // --- UPDATE ---
                    $encashment = LeaveEncashment::findOrFail($encashmentId);
                    $oldStatus = $encashment->status;
                    $oldDays = $encashment->days_encashed;

                    // Revert old balance if status was Approved/Paid before
                    if (in_array($oldStatus, [LeaveEncashment::STATUS_APPROVED, LeaveEncashment::STATUS_PAID])) {
                        $this->updateBalance(
                            $encashment->employee_id,
                            $encashment->leave_type_id,
                            -$oldDays // subtract what was added
                        );
                    }

                    $encashment->update($data);
                    $message = 'Leave encashment updated successfully.';

                    // Apply new balance if new status is Approved/Paid
                    $newStatus = $data['status'] ?? $oldStatus;
                    if (in_array($newStatus, [LeaveEncashment::STATUS_APPROVED, LeaveEncashment::STATUS_PAID])) {
                        $this->updateBalance(
                            $encashment->employee_id,
                            $encashment->leave_type_id,
                            $encashment->days_encashed
                        );
                    }
                } else {
                    // --- CREATE ---
                    $encashment = LeaveEncashment::create($data);
                    $message = 'Leave encashment request submitted successfully.';

                    // Apply balance if status is Approved or Paid
                    $status = $data['status'] ?? LeaveEncashment::STATUS_PENDING;
                    if (in_array($status, [LeaveEncashment::STATUS_APPROVED, LeaveEncashment::STATUS_PAID])) {
                        $this->updateBalance(
                            $encashment->employee_id,
                            $encashment->leave_type_id,
                            $encashment->days_encashed
                        );
                    }
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'data'    => $encashment->fresh()->load(['employee.personalInfo', 'leaveType', 'approvedBy']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving leave encashment: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Update employee_leave_balance.encashed_days
     * When encashment is Approved/Paid → add days to encashed_days
     * When status is reverted → subtract days from encashed_days
     */
    private function updateBalance(int $employeeId, int $leaveTypeId, float $daysToAdd): void
    {
        // Find the current fiscal year's balance record
        // Fallback: get the most recent balance for this employee+leave_type
        $balance = EmployeeLeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->orderByDesc('id')
            ->first();

        if ($balance) {
            $currentEncashed = $balance->encashed_days ?? 0;
            $newEncashed = max(0, $currentEncashed + $daysToAdd);
            $balance->update([
                'encashed_days' => $newEncashed,
                'updated_at'    => now(),
            ]);
        }
    }

    /**
     * Get single encashment by ID
     */
    public function getLeaveEncashmentById(int $id): array
    {
        try {
            $encashment = LeaveEncashment::with(['employee.personalInfo', 'leaveType', 'approvedBy'])
                ->findOrFail($id);

            return [
                'status' => 'success',
                'data'   => $encashment,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Leave encashment record not found.',
                'data'    => null,
            ];
        }
    }

    /**
     * Delete encashment (revert balance if needed)
     */
    public function deleteLeaveEncashment(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $encashment = LeaveEncashment::findOrFail($id);

                // Revert balance if was Approved/Paid
                if (in_array($encashment->status, [LeaveEncashment::STATUS_APPROVED, LeaveEncashment::STATUS_PAID])) {
                    $this->updateBalance(
                        $encashment->employee_id,
                        $encashment->leave_type_id,
                        -$encashment->days_encashed
                    );
                }

                $encashment->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Leave encashment deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting leave encashment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Approve encashment
     */
    public function approve(int $id, int $approvedBy): array
    {
        try {
            return DB::transaction(function () use ($id, $approvedBy) {
                $encashment = LeaveEncashment::findOrFail($id);

                if (!$encashment->isPending()) {
                    return [
                        'status'  => 'error',
                        'message' => 'Only pending encashment requests can be approved.',
                    ];
                }

                $encashment->update([
                    'status'      => LeaveEncashment::STATUS_APPROVED,
                    'approved_by' => $approvedBy,
                    'approved_at' => now(),
                ]);

                // Update balance: add encashed_days
                $this->updateBalance(
                    $encashment->employee_id,
                    $encashment->leave_type_id,
                    $encashment->days_encashed
                );

                return [
                    'status'  => 'success',
                    'message' => 'Leave encashment approved successfully. Balance updated.',
                    'data'    => $encashment->fresh()->load(['employee.personalInfo', 'leaveType']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error approving encashment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get encashments for a specific employee
     */
    public function getEncashmentsByEmployee(int $employeeId)
    {
        return LeaveEncashment::with(['leaveType', 'approvedBy'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Check remaining balance for an employee + leave type
     */
    public function getRemainingBalance(int $employeeId, int $leaveTypeId): float
    {
        $balance = EmployeeLeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->orderByDesc('id')
            ->first();

        return $balance ? (float) $balance->remaining_days : 0;
    }
}