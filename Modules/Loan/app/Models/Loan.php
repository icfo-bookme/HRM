<?php

namespace Modules\Loan\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Employee\Models\Employee;

class Loan extends Model
{
    use SoftDeletes;

    protected $table = 'loans';

    protected $fillable = [
        'loan_number',
        'employee_id',
        'loan_type',
        'loan_amount',
        'interest_rate',
        'total_interest',
        'total_payable',
        'installment_amount',
        'total_installments',
        'paid_installments',
        'remaining_amount',
        'purpose',
        'application_date',
        'approval_date',
        'first_installment_date',
        'disbursement_date',
        'status',
        'approved_by',
        'rejection_reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'loan_amount'           => 'decimal:2',
        'interest_rate'         => 'decimal:2',
        'total_interest'        => 'decimal:2',
        'total_payable'         => 'decimal:2',
        'installment_amount'    => 'decimal:2',
        'remaining_amount'      => 'decimal:2',
        'paid_installments'     => 'integer',
        'total_installments'    => 'integer',
        'application_date'      => 'date',
        'approval_date'         => 'date',
        'first_installment_date' => 'date',
        'disbursement_date'     => 'date',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id');
    }

    public function paidInstallments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id')->where('status', 'Paid');
    }

    public function pendingInstallments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id')->whereIn('status', ['Pending', 'Overdue']);
    }

    /**
     * Calculate total payable with interest.
     */
    public static function calculatePayable(float $amount, float $interestRate, int $installments): array
    {
        $totalInterest = $amount * ($interestRate / 100);
        $totalPayable = $amount + $totalInterest;
        $installmentAmount = $installments > 0 ? $totalPayable / $installments : $totalPayable;

        return [
            'loan_amount'        => $amount,
            'total_interest'     => round($totalInterest, 2),
            'total_payable'      => round($totalPayable, 2),
            'installment_amount' => round($installmentAmount, 2),
            'total_installments' => $installments,
        ];
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'Disbursed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Approved', 'Disbursed']);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}