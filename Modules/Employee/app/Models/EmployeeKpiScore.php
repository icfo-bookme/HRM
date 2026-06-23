<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeKpiScore extends Model
{
    use HasFactory;

    protected $table = 'employee_kpi_scores';
    public $timestamps = false;

    protected $fillable = [
        'review_id',
        'kpi_id',
        'actual_value',
        'score',
        'comments',
        'created_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function review()
    {
        return $this->belongsTo('Modules\Performance\Models\PerformanceReview', 'review_id');
    }

    public function kpi()
    {
        return $this->belongsTo('Modules\Performance\Models\Kpi', 'kpi_id');
    }

    public function scopeByScore($query, $minScore, $maxScore = null)
    {
        $query->where('score', '>=', $minScore);
        if ($maxScore) {
            $query->where('score', '<=', $maxScore);
        }
        return $query;
    }
}
