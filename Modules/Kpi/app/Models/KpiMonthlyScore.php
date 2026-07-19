<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;

class KpiMonthlyScore extends Model
{
    protected $table = 'kpi_monthly_scores';

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'working_days',
        'present_days',
        'late_days',
        'attendance_target',
        'attendance_obtained',
        'attendance_percentage',
        'total_assigned_tasks',
        'completed_tasks',
        'task_target',
        'task_obtained',
        'task_percentage',
        'behavior_given',
        'behavior_target',
        'behavior_obtained',
        'behavior_percentage',
        'bonus_given',
        'bonus_target',
        'bonus_obtained',
        'bonus_percentage',
        'penalty_given',
        'penalty_target',
        'penalty_obtained',
        'penalty_percentage',
        'total_target',
        'total_obtained',
        'overall_percentage',
        'rating',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'working_days' => 'integer',
        'present_days' => 'integer',
        'late_days' => 'integer',
        'total_assigned_tasks' => 'integer',
        'completed_tasks' => 'integer',
        'behavior_given' => 'boolean',
        'bonus_given' => 'boolean',
        'penalty_given' => 'boolean',
        'attendance_target' => 'decimal:2',
        'attendance_obtained' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'task_target' => 'decimal:2',
        'task_obtained' => 'decimal:2',
        'task_percentage' => 'decimal:2',
        'behavior_target' => 'decimal:2',
        'behavior_obtained' => 'decimal:2',
        'behavior_percentage' => 'decimal:2',
        'bonus_target' => 'decimal:2',
        'bonus_obtained' => 'decimal:2',
        'bonus_percentage' => 'decimal:2',
        'penalty_target' => 'decimal:2',
        'penalty_obtained' => 'decimal:2',
        'penalty_percentage' => 'decimal:2',
        'total_target' => 'decimal:2',
        'total_obtained' => 'decimal:2',
        'overall_percentage' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }
}
