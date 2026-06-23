<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;

class KpiMonthlyReview extends Model
{
    protected $table = 'kpi_monthly_reviews';

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'year',
        'month',
        'give_behavior',
        'behavior_score',
        'behavior_remarks',
        'give_bonus',
        'bonus_score',
        'bonus_remarks',
        'give_penalty',
        'penalty_score',
        'penalty_remarks',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'give_behavior' => 'boolean',
        'give_bonus' => 'boolean',
        'give_penalty' => 'boolean',
        'behavior_score' => 'decimal:1',
        'bonus_score' => 'decimal:1',
        'penalty_score' => 'decimal:1',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'Submitted');
    }
}
