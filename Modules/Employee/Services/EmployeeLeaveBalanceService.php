<?php

namespace Modules\Employee\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\EmployeeLeaveBalance;
use Yajra\DataTables\DataTables;

class EmployeeLeaveBalanceService
{
    public function getEmployeeLeaveBalanceDataTable(Request $request)
    {
        $query = EmployeeLeaveBalance::with(['employee', 'leaveType', 'fiscalYear'])
            ->select(
                'employee_leave_balance.id',
                'employee_leave_balance.employee_id',
                'employee_leave_balance.leave_type_id',
                'employee_leave_balance.fiscal_year_id',
                'employee_leave_balance.opening_balance',
                'employee_leave_balance.earned_days',
                'employee_leave_balance.used_days',
                'employee_leave_balance.encashed_days',
                'employee_leave_balance.lapsed_days',
                'employee_leave_balance.pending_days',
                'employee_leave_balance.remaining_days',
                'employee_leave_balance.updated_at',
            )
            ->orderByDesc('employee_leave_balance.id');

        if ($request->employee_id !== null && $request->employee_id !== '') {
            $query->where('employee_leave_balance.employee_id', $request->employee_id);
        }

        if ($request->leave_type_id !== null && $request->leave_type_id !== '') {
            $query->where('employee_leave_balance.leave_type_id', $request->leave_type_id);
        }

        if ($request->fiscal_year_id !== null && $request->fiscal_year_id !== '') {
            $query->where('employee_leave_balance.fiscal_year_id', $request->fiscal_year_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('employee', function ($balance) {
                return $balance->employee?->full_name ?? $balance->employee?->employee_code ?? 'N/A';
            })
            ->editColumn('leave_type', function ($balance) {
                return $balance->leaveType?->name ?? 'N/A';
            })
            ->editColumn('fiscal_year', function ($balance) {
                return $balance->fiscalYear?->name ?? (string) $balance->fiscal_year_id;
            })
            ->editColumn('opening_balance', function ($balance) {
                return number_format($balance->opening_balance, 1);
            })
            ->editColumn('earned_days', function ($balance) {
                return number_format($balance->earned_days, 1);
            })
            ->editColumn('used_days', function ($balance) {
                return number_format($balance->used_days, 1);
            })
            ->editColumn('encashed_days', function ($balance) {
                return number_format($balance->encashed_days, 1);
            })
            ->editColumn('lapsed_days', function ($balance) {
                return number_format($balance->lapsed_days, 1);
            })
            ->editColumn('pending_days', function ($balance) {
                return number_format($balance->pending_days, 1);
            })
            ->editColumn('remaining_days', function ($balance) {
                return number_format($balance->remaining_days, 1);
            })
            ->editColumn('updated_at', function ($balance) {
                return $balance->updated_at ? $balance->updated_at->format('d M Y H:i') : 'N/A';
            })
            ->addColumn('action', function ($balance) {
                return view('components.action-buttons', [
                    'id'     => $balance->id,
                    'edit'   => 'employeeLeaveBalanceEdit',
                    'delete' => 'employeeLeaveBalanceDelete',
                ])->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveEmployeeLeaveBalance(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $balanceId = $data['balance_id'] ?? null;

                if ($balanceId) {
                    $balance = EmployeeLeaveBalance::findOrFail($balanceId);
                    $balance->update($data);
                    $message = 'Leave balance updated successfully.';
                    $status  = 'success';
                } else {
                    $balance = EmployeeLeaveBalance::create($data);
                    $message = 'Leave balance created successfully.';
                    $status  = 'success';
                }

                return [
                    'status'  => $status,
                    'message' => $message,
                    'data'    => $balance->fresh()->load(['employee', 'leaveType', 'fiscalYear']),
                ];
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $errorMsg = $e->getMessage();

            if (str_contains($errorMsg, 'uk_leave_balance') || str_contains($errorMsg, 'Duplicate entry')) {
                return [
                    'status'  => 'error',
                    'message' => 'A leave balance record already exists for this employee, leave type, and fiscal year combination.',
                    'data'    => null,
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Error saving leave balance: ' . $e->getMessage(),
                'data'    => null,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving leave balance: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function getEmployeeLeaveBalanceById(int $id): array
    {
        try {
            $balance = EmployeeLeaveBalance::with(['employee', 'leaveType', 'fiscalYear'])
                ->findOrFail($id);

            return [
                'status' => 'success',
                'data'   => $balance,
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Leave balance record not found.',
                'data'    => null,
            ];
        }
    }

    public function deleteEmployeeLeaveBalance(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                EmployeeLeaveBalance::findOrFail($id)->delete();

                return [
                    'status'  => 'success',
                    'message' => 'Leave balance record deleted successfully.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error deleting leave balance: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get balances for a specific employee
     */
    public function getBalancesByEmployee(int $employeeId)
    {
        return EmployeeLeaveBalance::with(['leaveType', 'fiscalYear'])
            ->where('employee_id', $employeeId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Get balance for a specific employee + leave_type + fiscal_year
     */
    public function getBalance(int $employeeId, int $leaveTypeId, int $fiscalYearId): ?EmployeeLeaveBalance
    {
        return EmployeeLeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->first();
    }
}