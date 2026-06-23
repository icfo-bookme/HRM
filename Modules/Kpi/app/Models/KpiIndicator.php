<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiIndicator extends Model
{
    protected $table = 'kpi_indicators';

    protected $fillable = [
        'category_id',
        'key',
        'name',
        'name_bn',
        'weight_percentage',
        'point_per_unit',
        'default_max_score',
        'count_behavior',
        'is_active',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'point_per_unit' => 'decimal:2',
        'default_max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KpiCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAlwaysCount($query)
    {
        return $query->where('count_behavior', 'Always Count');
    }

    public function scopeOptionalCount($query)
    {
        return $query->where('count_behavior', 'Optional Count');
    }
}
