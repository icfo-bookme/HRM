<?php

namespace Modules\Holidays\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Branch\Models\Branch;
use Modules\Department\Models\Department;

class HolidayAssignment extends Model
{
    protected $table = 'holiday_assignments';

    protected $fillable = [
        'holiday_id',
        'branch_id',
        'department_id',
    ];

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(Holiday::class, 'holiday_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}