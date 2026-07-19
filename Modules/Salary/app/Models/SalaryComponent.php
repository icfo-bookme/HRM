<?php

namespace Modules\Salary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $table = 'salary_components';

    protected $fillable = [
        'name',
        'type',
        'category',
        'calculation_type',
        'default_value',
        'formula_expression',
        'is_taxable',
        'is_pf_basis',
        'is_active',
        'show_in_slip',
        'display_order',
        'metadata',
    ];

    protected $casts = [
        'default_value'    => 'decimal:4',
        'is_taxable'       => 'boolean',
        'is_pf_basis'      => 'boolean',
        'is_active'        => 'boolean',
        'show_in_slip'     => 'boolean',
        'display_order'    => 'integer',
        'metadata'         => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    /**
     * Get formatted default value
     */
    public function getFormattedDefaultValueAttribute(): string
    {
        return number_format($this->default_value, 2);
    }

    /**
     * Scope a query to only include active components.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to order by display_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}