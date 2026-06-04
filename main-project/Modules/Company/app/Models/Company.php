<?php

namespace Modules\Company\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    /**
     * Table name (optional but safe in modules)
     */
    protected $table = 'companies';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'legal_name',
        'trade_license',
        'bin_number',
        'tin_number',
        'industry',
        'founded_year',
        'logo_path',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'timezone',
        'date_format',
        'fiscal_year_start',
        'is_active',
        'settings',
    ];

    /**
     * Cast fields
     */
    protected $casts = [
        'founded_year'       => 'integer',
        'is_active'          => 'boolean',
        'settings'           => 'array',
        'fiscal_year_start'  => 'date',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /**
     * Default values (optional)
     */
    protected $attributes = [
        'country' => 'Bangladesh',
        'timezone' => 'Asia/Dhaka',
        'date_format' => 'Y-m-d',
        'is_active' => 1,
    ];

    /**
     * 🔥 Scopes
     */

    // Active companies
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Inactive companies
    public function scopeInactive($query)
    {
        return $query->where('is_active', 0);
    }

    // Filter by city
    public function scopeCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * 🔗 Relationships (future-ready)
     */

    // Example: Company has many branches
    public function branches()
    {
        return $this->hasMany(\Modules\Branch\Models\Branch::class);
    }
}