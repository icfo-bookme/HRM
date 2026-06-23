<?php

namespace Modules\Holidays\Models;

use App\Traits\CustomSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    use HasFactory;
    use CustomSoftDeletes;

    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'holiday_date',
        'end_date',
        'holiday_type',
        'applicable_to',
        'is_recurring',
        'yearly_recurring',
        'description',
        'deleted_at',
    ];

    protected $casts = [
        'holiday_date'     => 'date',
        'end_date'         => 'date',
        'is_recurring'     => 'boolean',
        'yearly_recurring' => 'boolean',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(HolidayAssignment::class, 'holiday_id', 'id');
    }
}