<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'attendance_date',
        'first_in_at',
        'last_out_at',
        'check_in_at',
        'check_out_at',
        'break_minutes',
        'working_minutes',
        'net_working_minutes',
        'late_minutes',
        'early_out_minutes',
        'overtime_minutes',
        'is_late',
        'is_early_out',
        'is_absent',
        'is_holiday_work',
        'attendance_status',
        'approval_status',
        'approved_by',
        'approved_at',
        'remarks',
        'source',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'first_in_at' => 'datetime',
        'last_out_at' => 'datetime',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_late' => 'boolean',
        'is_early_out' => 'boolean',
        'is_absent' => 'boolean',
        'is_holiday_work' => 'boolean',
        'break_minutes' => 'integer',
        'working_minutes' => 'integer',
        'net_working_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_out_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}