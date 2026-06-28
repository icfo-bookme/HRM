<?php

namespace Modules\Salary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Setting\Models\FiscalYear;

class PayrollRun extends Model
{
    protected $table = 'payroll_runs';

    protected $fillable = [
        'fiscal_year_id',
        'run_month',
        'run_label',
        'run_type',
        'total_employees',
        'total_gross',
        'total_net',
        'total_deductions',
        'status',
        'approved_by',
        'approved_at',
        'disbursed_by',
        'disbursed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'run_month'       => 'date',
        'approved_at'     => 'datetime',
        'disbursed_at'    => 'datetime',
        'total_gross'     => 'decimal:2',
        'total_net'       => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by', 'id');
    }

    public function disbursedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'disbursed_by', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    /**
     * Per-employee detail records; populated when the run is Locked.
     */
    public function details()
    {
        return $this->hasMany(PayrollRunDetail::class, 'payroll_run_id', 'id');
    }

    /**
     * Determine whether this payroll run has a frozen snapshot.
     */
    public function hasSnapshot(): bool
    {
        return $this->status === 'Locked' && $this->details()->exists();
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('run_month', $year)->whereMonth('run_month', $month);
    }
}