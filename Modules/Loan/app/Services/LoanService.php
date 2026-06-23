<?php

namespace Modules\Loan\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Models\Loan;
use Modules\Loan\Models\LoanInstallment;
use Modules\Employee\Models\Employee;
use Yajra\DataTables\DataTables;

class LoanService
{
    /**
     * Get loans data for DataTable (Admin view)
     */
    public function getLoanDataTable(Request $request)
    {
        $query = Loan::with([
            'employee.personalInfo',
            'employee.department',
            'approvedBy',
            'createdBy'
        ])->select('loans.*');

        if ($request->filled('status')) {
            $query->where('loans.status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('loans.employee_id', $request->employee_id);
        }

        if ($request->filled('loan_type')) {
            $query->where('loans.loan_type', $request->loan_type);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('loan_number', fn($loan) => '<span class="font-mono text-xs">' . e($loan->loan_number) . '</span>')
            ->editColumn('employee_id', function ($loan) {
                $emp = $loan->employee;
                if (!$emp) return 'N/A';
                $code = $emp->employee_code ?? '';
                $name = $emp->personalInfo?->full_name ?? 'N/A';
                return $code ? "$code - $name" : $name;
            })
            ->editColumn('loan_type', function ($loan) {
                $colors = [
                    'Personal'  => 'bg-blue-100 text-blue-700',
                    'Emergency' => 'bg-red-100 text-red-700',
                    'Education' => 'bg-green-100 text-green-700',
                    'Medical'   => 'bg-yellow-100 text-yellow-700',
                    'Vehicle'   => 'bg-purple-100 text-purple-700',
                    'Home'      => 'bg-indigo-100 text-indigo-700',
                    'Other'     => 'bg-gray-100 text-gray-700',
                ];
                $color = $colors[$loan->loan_type] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $loan->loan_type . '</span>';
            })
            ->editColumn('loan_amount', fn($loan) => number_format($loan->loan_amount, 2))
            ->editColumn('total_payable', fn($loan) => number_format($loan->total_payable, 2))
            ->editColumn('installment_amount', fn($loan) => number_format($loan->installment_amount, 2))
            ->editColumn('remaining_amount', fn($loan) => number_format($loan->remaining_amount, 2))
            ->editColumn('application_date', fn($loan) => $loan->application_date->format('d M Y'))
            ->editColumn('status', function ($loan) {
                $colors = [
                    'Pending'    => 'bg-yellow-100 text-yellow-700',
                    'Approved'   => 'bg-green-100 text-green-700',
                    'Rejected'   => 'bg-red-100 text-red-700',
                    'Disbursed'  => 'bg-blue-100 text-blue-700',
                    'Completed'  => 'bg-gray-800 text-white',
                    'Cancelled'  => 'bg-gray-100 text-gray-500',
                ];
                $color = $colors[$loan->status] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $loan->status . '</span>';
            })
            ->addColumn('progress', function ($loan) {
                if ($loan->total_installments <= 0) return 'N/A';
                $percent = round(($loan->paid_installments / $loan->total_installments) * 100, 1);
                $color = $percent >= 100 ? 'bg-green-500' : ($percent >= 50 ? 'bg-blue-500' : 'bg-yellow-500');
                return '<div class="flex items-center gap-2"><div class="w-24 bg-gray-200 rounded-full h-2"><div class="' . $color . ' h-2 rounded-full" style="width:' . $percent . '%"></div></div><span class="text-xs text-gray-600">' . $loan->paid_installments . '/' . $loan->total_installments . '</span></div>';
            })
            ->addColumn('action', function ($loan) {
                $btn = '<a href="' . route('loan.show', $loan->id) . '" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 mr-1" title="View"><i class="fas fa-eye"></i></a>';

                if ($loan->status === 'Pending') {
                    $btn .= '<button onclick="loanApprove(' . $loan->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 mr-1" title="Approve"><i class="fas fa-check"></i></button>';
                    $btn .= '<button onclick="loanReject(' . $loan->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 mr-1" title="Reject"><i class="fas fa-times"></i></button>';
                    $btn .= '<button onclick="loanDelete(' . $loan->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200" title="Delete"><i class="fas fa-trash"></i></button>';
                }

                if ($loan->status === 'Approved') {
                    $btn .= '<button onclick="loanDisburse(' . $loan->id . ')" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200" title="Disburse"><i class="fas fa-hand-holding-usd"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['loan_type', 'loan_number', 'status', 'progress', 'action'])
            ->make(true);
    }

    /**
     * Get my loans data for DataTable (Employee view)
     */
    public function getMyLoanDataTable(Request $request, int $employeeId)
    {
        $query = Loan::with(['approvedBy', 'createdBy'])
            ->where('employee_id', $employeeId)
            ->select('loans.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('loan_number', fn($loan) => '<span class="font-mono text-xs">' . e($loan->loan_number) . '</span>')
            ->editColumn('loan_amount', fn($loan) => number_format($loan->loan_amount, 2))
            ->editColumn('total_payable', fn($loan) => number_format($loan->total_payable, 2))
            ->editColumn('installment_amount', fn($loan) => number_format($loan->installment_amount, 2))
            ->editColumn('remaining_amount', fn($loan) => number_format($loan->remaining_amount, 2))
            ->editColumn('application_date', fn($loan) => $loan->application_date->format('d M Y'))
            ->editColumn('status', function ($loan) {
                $colors = [
                    'Pending'    => 'bg-yellow-100 text-yellow-700',
                    'Approved'   => 'bg-green-100 text-green-700',
                    'Rejected'   => 'bg-red-100 text-red-700',
                    'Disbursed'  => 'bg-blue-100 text-blue-700',
                    'Completed'  => 'bg-gray-800 text-white',
                    'Cancelled'  => 'bg-gray-100 text-gray-500',
                ];
                $color = $colors[$loan->status] ?? 'bg-gray-100 text-gray-700';
                return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $color . '">' . $loan->status . '</span>';
            })
            ->addColumn('progress', function ($loan) {
                if ($loan->total_installments <= 0) return 'N/A';
                $percent = round(($loan->paid_installments / $loan->total_installments) * 100, 1);
                $color = $percent >= 100 ? 'bg-green-500' : ($percent >= 50 ? 'bg-blue-500' : 'bg-yellow-500');
                return '<div class="flex items-center gap-2"><div class="w-24 bg-gray-200 rounded-full h-2"><div class="' . $color . ' h-2 rounded-full" style="width:' . $percent . '%"></div></div><span class="text-xs text-gray-600">' . $loan->paid_installments . '/' . $loan->total_installments . '</span></div>';
            })
            ->addColumn('action', function ($loan) {
                return '<a href="' . route('loan.show', $loan->id) . '" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200" title="View"><i class="fas fa-eye"></i> View</a>';
            })
            ->rawColumns(['loan_number', 'status', 'progress', 'action'])
            ->make(true);
    }

    /**
     * Generate a professional loan number: LN-YYYY-XXXX
     */
    private function generateLoanNumber(): string
    {
        $year = now()->format('Y');
        $lastLoan = Loan::where('loan_number', 'like', "LN-{$year}-%")
            ->orderBy('loan_number', 'desc')
            ->first();
        
        if ($lastLoan) {
            // Extract sequence number properly regardless of digit count (e.g., 0001, 10000)
            $parts = explode('-', $lastLoan->loan_number);
            $sequence = (int) end($parts) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('LN-%s-%04d', $year, $sequence);
    }

    /**
     * Save a loan application (create or update)
     */
    public function saveLoan(array $data): array
    { 
        try {
            return DB::transaction(function () use ($data) {
                $loanId = $data['loan_id'] ?? null;
                $isUpdate = $loanId !== null;

                if ($isUpdate) {
                    $loan = Loan::findOrFail($loanId);
                    if (!in_array($loan->status, ['Pending', 'Rejected'])) {
                        return ['status' => 'error', 'message' => 'Only pending or rejected loans can be edited.'];
                    }
                    
                    // Recalculate financials if amount, rate, or installments changed
                    $interestRate = $data['interest_rate'] ?? 0;
                    $installments = $data['total_installments'] ?? 1;
                    $calculations = Loan::calculatePayable($data['loan_amount'], $interestRate, $installments);
                    
                    $data['total_interest'] = $calculations['total_interest'];
                    $data['total_payable'] = $calculations['total_payable'];
                    $data['installment_amount'] = $calculations['installment_amount'];
                    $data['remaining_amount'] = $calculations['total_payable'];
                    
                    unset($data['loan_number'], $data['loan_id']);
                    $loan->update($data);
                    $message = 'Loan application updated successfully.';
                } else {
                    // Calculate payable amounts
                    $interestRate = $data['interest_rate'] ?? 0;
                    $installments = $data['total_installments'] ?? 1;
                    $calculations = Loan::calculatePayable($data['loan_amount'], $interestRate, $installments);

                    $data['loan_number'] = $this->generateLoanNumber();
                    $data['total_interest'] = $calculations['total_interest'];
                    $data['total_payable'] = $calculations['total_payable'];
                    $data['installment_amount'] = $calculations['installment_amount'];
                    $data['remaining_amount'] = $calculations['total_payable'];
                    $data['status'] = 'Pending';

                    $loan = Loan::create($data);
                    $message = 'Loan application submitted successfully.';
                }

                return [
                    'status'  => 'success',
                    'message' => $message,
                    'loan'    => $loan->fresh()->load(['employee.personalInfo', 'approvedBy']),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Error saving loan: ' . $e->getMessage(),
                'loan'    => null,
            ];
        }
    }

    /**
     * Approve a loan application
     */
    public function approveLoan(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);
                if ($loan->status !== 'Pending') {
                    return ['status' => 'error', 'message' => 'Only pending loans can be approved.'];
                }

                // Calculate first installment date (next month after approval)
                $firstInstallmentDate = Carbon::now()->addMonth()->startOfMonth();

                $loan->update([
                    'status'                => 'Approved',
                    'approved_by'           => auth()->id(),
                    'approval_date'         => now(),
                    'first_installment_date'=> $firstInstallmentDate,
                ]);

                // Generate installments
                $installmentAmount = $loan->installment_amount;
                $totalInstallments = $loan->total_installments;

                for ($i = 1; $i <= $totalInstallments; $i++) {
                    $dueDate = $firstInstallmentDate->copy()->addMonths($i - 1);
                    LoanInstallment::create([
                        'loan_id'         => $loan->id,
                        'installment_no'  => $i,
                        'due_date'        => $dueDate,
                        'amount'          => $installmentAmount,
                        'paid_amount'     => 0,
                        'status'          => 'Pending',
                    ]);
                }

                return [
                    'status'  => 'success',
                    'message' => 'Loan approved successfully. ' . $totalInstallments . ' installments generated.',
                    'loan'    => $loan->fresh()->load(['employee.personalInfo', 'installments']),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error approving loan: ' . $e->getMessage()];
        }
    }

    /**
     * Reject a loan application
     */
    public function rejectLoan(int $id, ?string $reason = null): array
    {
        try {
            return DB::transaction(function () use ($id, $reason) {
                $loan = Loan::findOrFail($id);
                if ($loan->status !== 'Pending') {
                    return ['status' => 'error', 'message' => 'Only pending loans can be rejected.'];
                }

                $loan->update([
                    'status'           => 'Rejected',
                    'approved_by'      => auth()->id(),
                    'approval_date'    => now(),
                    'rejection_reason' => $reason,
                ]);

                return [
                    'status'  => 'success',
                    'message' => 'Loan application rejected.',
                    'loan'    => $loan->fresh(),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error rejecting loan: ' . $e->getMessage()];
        }
    }

    /**
     * Disburse a loan (mark as disbursed)
     */
    public function disburseLoan(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);
                if ($loan->status !== 'Approved') {
                    return ['status' => 'error', 'message' => 'Only approved loans can be disbursed.'];
                }

                $loan->update([
                    'status'            => 'Disbursed',
                    'disbursement_date' => now(),
                ]);

                return [
                    'status'  => 'success',
                    'message' => 'Loan disbursed successfully.',
                    'loan'    => $loan->fresh()->load(['employee.personalInfo', 'installments']),
                ];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error disbursing loan: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a loan
     */
    public function deleteLoan(int $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                $loan = Loan::findOrFail($id);
                if (!in_array($loan->status, ['Pending', 'Rejected', 'Cancelled'])) {
                    return ['status' => 'error', 'message' => 'Only pending, rejected, or cancelled loans can be deleted.'];
                }
                $loan->delete();
                return ['status' => 'success', 'message' => 'Loan deleted successfully.'];
            });
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error deleting loan: ' . $e->getMessage()];
        }
    }

    /**
     * Get loan by ID with all relations
     */
    public function getLoanById(int $id): array
    {
        try {
            $loan = Loan::with([
                'employee.personalInfo',
                'employee.department',
                'employee.designation',
                'employee.branch',
                'approvedBy',
                'createdBy',
                'installments.payrollRun',
            ])->findOrFail($id);

            // Calculate summary
            $totalInstallments = $loan->installments->count();
            $paidInstallments = $loan->installments->where('status', 'Paid')->count();
            $pendingInstallments = $loan->installments->whereIn('status', ['Pending', 'Overdue'])->count();
            $totalPaid = $loan->installments->where('status', 'Paid')->sum('paid_amount');
            $totalPending = $loan->installments->whereIn('status', ['Pending', 'Overdue'])->sum('amount');

            return [
                'status'                => 'success',
                'loan'                  => $loan,
                'summary'               => [
                    'total_installments'  => $totalInstallments,
                    'paid_installments'   => $paidInstallments,
                    'pending_installments'=> $pendingInstallments,
                    'total_paid'          => round($totalPaid, 2),
                    'total_pending'       => round($totalPending, 2),
                ],
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Loan not found.', 'loan' => null];
        }
    }

    /**
     * Get loan deductions for an employee for a given month (for payroll integration)
     */
    public function getEmployeeLoanDeductions(int $employeeId, string $runMonth): float
    {
        $start = Carbon::parse($runMonth)->startOfMonth();
        $end = Carbon::parse($runMonth)->endOfMonth();

        $installments = LoanInstallment::whereHas('loan', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId)
              ->whereIn('status', ['Approved', 'Disbursed']);
        })
        ->whereBetween('due_date', [$start, $end])
        ->whereIn('status', ['Pending', 'Overdue'])
        ->get();

        return round($installments->sum('amount'), 2);
    }

    /**
     * Get loans due for a given month (for payroll generation)
     */
    public function getLoansDueForMonth(string $runMonth): array
    {
        $start = Carbon::parse($runMonth)->startOfMonth();
        $end = Carbon::parse($runMonth)->endOfMonth();

        $installments = LoanInstallment::with(['loan.employee.personalInfo'])
            ->whereBetween('due_date', [$start, $end])
            ->whereIn('status', ['Pending', 'Overdue'])
            ->get()
            ->groupBy('loan_id');

        $result = [];
        foreach ($installments as $loanId => $loanInstallments) {
            $loan = $loanInstallments->first()->loan;
            if (!$loan || !in_array($loan->status, ['Approved', 'Disbursed'])) continue;

            $result[] = [
                'loan'         => $loan,
                'installments' => $loanInstallments,
                'total_due'    => $loanInstallments->sum('amount'),
            ];
        }

        return $result;
    }

    /**
     * Mark installments as paid from payroll
     */
    public function markInstallmentsPaid(int $loanId, array $installmentIds, int $payrollRunId): void
    {
        $installments = LoanInstallment::where('loan_id', $loanId)
            ->whereIn('id', $installmentIds)
            ->get();

        foreach ($installments as $installment) {
            $installment->update([
                'status'         => 'Paid',
                'paid_amount'    => $installment->amount,
                'payroll_run_id' => $payrollRunId,
                'paid_at'        => now(),
            ]);
        }

        // Update loan progress
        $loan = Loan::find($loanId);
        if ($loan) {
            $paidCount = LoanInstallment::where('loan_id', $loanId)->where('status', 'Paid')->count();
            $remainingAmount = LoanInstallment::where('loan_id', $loanId)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->sum('amount');

            $loan->update([
                'paid_installments'  => $paidCount,
                'remaining_amount'   => $remainingAmount,
            ]);

            // Check if loan is completed
            if ($paidCount >= $loan->total_installments && $remainingAmount <= 0) {
                $loan->update(['status' => 'Completed']);
            }
        }
    }

    /**
     * Revert installments previously marked as paid by a payroll run
     */
    public function revertInstallmentsForPayrollRun(int $payrollRunId): void
    {
        $installments = LoanInstallment::where('payroll_run_id', $payrollRunId)->get();
        $loanIds = $installments->pluck('loan_id')->unique();

        if ($installments->isEmpty()) {
            return;
        }

        // Reset installments status and linkage
        LoanInstallment::where('payroll_run_id', $payrollRunId)->update([
            'status'         => 'Pending',
            'paid_amount'    => 0,
            'payroll_run_id' => null,
            'paid_at'        => null,
        ]);

        // Update affected loans to reflect the reverted payments
        foreach ($loanIds as $loanId) {
            $loan = Loan::find($loanId);
            if (!$loan) continue;

            $paidCount = LoanInstallment::where('loan_id', $loanId)->where('status', 'Paid')->count();
            $remainingAmount = LoanInstallment::where('loan_id', $loanId)
                ->whereIn('status', ['Pending', 'Overdue'])
                ->sum('amount');

            $updateData = [
                'paid_installments' => $paidCount,
                'remaining_amount'  => $remainingAmount,
            ];

            // If the loan was 'Completed', revert it to 'Disbursed' since there's now outstanding balance
            if ($loan->status === 'Completed' && ($paidCount < $loan->total_installments || $remainingAmount > 0)) {
                $updateData['status'] = 'Disbursed';
            }

            $loan->update($updateData);
        }
    }

    /**
     * Get employee's active loans summary
     */
    public function getEmployeeLoanSummary(int $employeeId): array
    {
        $activeLoans = Loan::where('employee_id', $employeeId)
            ->whereIn('status', ['Approved', 'Disbursed'])
            ->get();

        $totalRemaining = $activeLoans->sum('remaining_amount');
        $totalPayable = $activeLoans->sum('total_payable');
        $totalPaid = $totalPayable - $totalRemaining;
        $monthlyDeduction = $activeLoans->sum('installment_amount');

        return [
            'active_loans'      => $activeLoans->count(),
            'total_remaining'   => round($totalRemaining, 2),
            'total_payable'     => round($totalPayable, 2),
            'total_paid'        => round($totalPaid, 2),
            'monthly_deduction' => round($monthlyDeduction, 2),
        ];
    }

    /**
     * Get loan statistics for dashboard
     */
    public function getLoanStatistics(): array
    {
        $totalLoans = Loan::count();
        $pendingLoans = Loan::pending()->count();
        $activeLoans = Loan::active()->count();
        $completedLoans = Loan::where('status', 'Completed')->count();
        $totalDisbursed = Loan::whereIn('status', ['Disbursed', 'Completed'])->sum('loan_amount');
        $totalOutstanding = Loan::whereIn('status', ['Approved', 'Disbursed'])->sum('remaining_amount');

        return [
            'total_loans'      => $totalLoans,
            'pending_loans'    => $pendingLoans,
            'active_loans'     => $activeLoans,
            'completed_loans'  => $completedLoans,
            'total_disbursed'  => round($totalDisbursed, 2),
            'total_outstanding'=> round($totalOutstanding, 2),
        ];
    }
}