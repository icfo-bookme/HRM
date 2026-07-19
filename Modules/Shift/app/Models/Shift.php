<?php

namespace Modules\Shift\Models;

use App\Traits\CustomSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Company\Models\Company;

// use Modules\Shift\Database\Factories\ShiftFactory;

class Shift extends Model
{
    use HasFactory;
    use CustomSoftDeletes;

    protected $table = 'shifts';

    protected $fillable = [      
        'name',
        'start_time',
        'end_time',
        'break_minutes',
        'grace_in_min',
        'grace_out_min',
        'work_hours',
        'is_night_shift',
        'is_flexible',
        'is_active',
        'metadata',
        'deleted_at',
    ];

    protected $casts = [
        'break_minutes'  => 'integer',
        'grace_in_min'   => 'integer',
        'grace_out_min'  => 'integer',
        'work_hours'     => 'float',
        'is_night_shift' => 'boolean',
        'is_flexible'    => 'boolean',
        'is_active'      => 'boolean',
        'metadata'       => 'array', 
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

  


   
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function scopeNightShift($query)
    {
        return $query->where('is_night_shift', true);
    }
}
