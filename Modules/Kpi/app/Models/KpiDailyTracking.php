<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;
use Modules\Attendance\Models\Attendance;

class KpiDailyTracking extends Model
{
    protected $table = 'kpi_daily_tracking';

    protected $fillable = [
        'employee_id',
        'tracking_date',
        'is_working_day',
        'is_present',
        'is_late',
        'present_target',
        'present_obtained',
        'late_target',
        'late_obtained',
        'daily_target',
        'daily_obtained',
        'daily_percentage',
    ];

    protected $casts = [
        'tracking_date' => 'date',
        'is_working_day' => 'boolean',
        'is_present' => 'boolean',
        'is_late' => 'boolean',
        'present_target' => 'decimal:1',
        'present_obtained' => 'decimal:1',
        'late_target' => 'decimal:1',
        'late_obtained' => 'decimal:1',
        'daily_target' => 'decimal:2',
        'daily_obtained' => 'decimal:2',
        'daily_percentage' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the attendance record for this tracking entry
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'employee_id', 'employee_id')
            ->whereColumn('attendance_date
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('tracking_date', $year)
            ->whereMonth('tracking_date', $month);
    }
}
