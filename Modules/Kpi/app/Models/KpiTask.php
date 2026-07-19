<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;

class KpiTask extends Model
{
    protected $table = 'kpi_tasks';

    protected $fillable = [
        'employee_id',
        'assigned_by',
        'title',
        'description',
        'target_score',
        'obtained_score',
        'priority',
        'assigned_date',
        'deadline',
        'status',
        'completed_at',
        'completion_note',
    ];

    protected $casts = [
        'target_score' => 'decimal:2',
        'obtained_score' => 'decimal:2',
        'assigned_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_by');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['Pending', 'In Progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('assigned_date', $year)
            ->whereMonth('assigned_date', $month);
    }
}
