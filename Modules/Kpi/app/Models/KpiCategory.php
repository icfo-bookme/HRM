<?php

namespace Modules\Kpi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCategory extends Model
{
    protected $table = 'kpi_categories';

    protected $fillable = [
        'name',
        'name_bn',
        'weight_percentage',
        'calculation_type',
        'point_setting',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function indicators(): HasMany
    {
        return $this->hasMany(KpiIndicator::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
