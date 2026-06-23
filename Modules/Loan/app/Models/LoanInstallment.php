<?php

namespace Modules\Loan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Salary\Models\PayrollRun;

class LoanInstallment extends Model
{
    protected $table = 'loan_installments';

    protected $fillable = [
        'loan_id',
        'installment_no',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'payroll_run_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date'    => 'date',
        'paid_at'     => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'Overdue');
    }

    public function scopeByLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopeDueForMonth($query, string $yearMonth)
    {
        $start = \Carbon\Carbon::parse($yearMonth)->startOfMonth();
        $end = \Carbon\Carbon::parse($yearMonth)->endOfMonth();
        return $query->whereBetween('due_date', [$start, $end])
            ->whereIn('status', ['Pending', 'Overdue']);
    }
}