<?php

namespace Modules\Setting\Models;

use App\Traits\CustomSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Company\Models\Company;

class FiscalYear extends Model
{
    use HasFactory;
    

    protected $table = 'fiscal_years';

    protected $fillable = [
        'company_id',
        'label',
        'start_date',
        'end_date',
        'is_current',
        'locked',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date'   => 'date:Y-m-d',
        'is_current' => 'boolean',
        'locked'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONS =====
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // ===== SCOPES =====
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}