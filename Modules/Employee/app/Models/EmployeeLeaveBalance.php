<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeLeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'employee_leave_balance';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'fiscal_year_id',
        'opening_balance',
        'earned_days',
        'used_days',
        'encashed_days',
        'lapsed_days',
        'pending_days',
        'updated_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:1',
        'earned_days' => 'decimal:1',
        'used_days' => 'decimal:1',
        'encashed_days' => 'decimal:1',
        'lapsed_days' => 'decimal:1',
        'pending_days' => 'decimal:1',
        'remaining_days' => 'decimal:1',   // Generated column (read-only)
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo('Modules\Leave\Models\LeaveType', 'leave_type_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo('Modules\Setting\Models\FiscalYear', 'fiscal_year_id');
    }

    public function getRemainingDaysAttribute()
    {
        return $this->opening_balance 
            + $this->earned_days 
            - $this->used_days 
            - $this->encashed_days 
            - $this->lapsed_days 
            - $this->pending_days;
    }
}
