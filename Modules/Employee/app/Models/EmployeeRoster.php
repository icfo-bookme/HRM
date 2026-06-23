<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeRoster extends Model
{
    use HasFactory;

    protected $table = 'employee_rosters';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'roster_date',
        'is_day_off',
        'created_by',
    ];

    protected $casts = [
        'roster_date' => 'date',
        'is_day_off' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function shift()
    {
        return $this->belongsTo('Modules\Shift\Models\Shift', 'shift_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('roster_date', $date);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('roster_date', [$from, $to]);
    }

    public function scopeDayOff($query)
    {
        return $query->where('is_day_off', true);
    }

    public function scopeWorkingDays($query)
    {
        return $query->where('is_day_off', false);
    }
}
