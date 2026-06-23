<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'description',
        'days_per_year',
        'is_paid',
        'is_half_day_allowed',
        'carry_forward',
        'max_carry_days',
        'max_consecutive_days',
        'requires_document',
        'min_days_notice',
        'applicable_gender',
        'color_code',
        'is_active',
        'affects_balance',
    ];

    protected $casts = [
        'days_per_year' => 'decimal:1',
        'is_paid' => 'boolean',
        'is_half_day_allowed' => 'boolean',
        'carry_forward' => 'boolean',
        'max_carry_days' => 'decimal:1',
        'max_consecutive_days' => 'integer',
        'requires_document' => 'boolean',
        'min_days_notice' => 'integer',
        'applicable_gender' => 'string',
        'is_active' => 'boolean',
        'affects_balance' => 'boolean',
    ];

    public function leaveBalances()
    {
        return $this->hasMany('Modules\Employee\Models\EmployeeLeaveBalance', 'leave_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
}