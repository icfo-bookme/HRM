<?php

namespace Modules\Salary\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;

class PayrollRunDetail extends Model
{
    protected $table = 'payroll_run_details';

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'employee_name',
        'employee_code',
        'basic_salary',
        'gross',
        'deductions',
        'net',
        'component_details',
        'attendance_summary',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'basic_salary'       => 'decimal:4',
        'gross'              => 'decimal:4',
        'deductions'         => 'decimal:4',
        'net'                => 'decimal:4',
        'component_details'  => 'json',
        'attendance_summary' => 'json',
        'payment_status'     => 'integer',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /**
     * The payroll run this detail record belongs to.
     */
    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id', 'id');
    }

    /**
     * The employee this record is for.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * The user who locked / created this snapshot.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
