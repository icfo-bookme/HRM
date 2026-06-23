<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWeekend extends Model
{
    protected $table = 'employee_weekends';

    protected $fillable = [
        'employee_id',
        'weekend_days',
    ];

    protected $casts = [
        'weekend_days' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}