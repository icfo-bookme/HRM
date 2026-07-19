<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendanceRule extends Model
{
    protected $table = 'employee_attendance_rules';

    protected $fillable = [
        'employee_id',
        'enable_overtime',
        'overtime_rate_per_hour',
        'overtime_multiplier',
        'enable_late_deduction',
        'late_deduction_type',
        'late_deduction_per_minute',
        'late_deduction_fixed',
        'late_grace_minutes',
        'enable_half_day_deduction',
        'half_day_deduction_percent',
        'enable_absent_deduction',
        'absent_deduction_days',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enable_overtime' => 'boolean',
        'overtime_rate_per_hour' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'enable_late_deduction' => 'boolean',
        'late_deduction_per_minute' => 'decimal:4',
        'late_deduction_fixed' => 'decimal:2',
        'late_grace_minutes' => 'integer',
        'enable_half_day_deduction' => 'boolean',
        'half_day_deduction_percent' => 'decimal:2',
        'enable_absent_deduction' => 'boolean',
        'absent_deduction_days' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}