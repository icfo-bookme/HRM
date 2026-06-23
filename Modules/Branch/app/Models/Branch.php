<?php

namespace Modules\Branch\Models;

use App\Traits\CustomSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Company\Models\Company;

class Branch extends Model
{
    use HasFactory;
    use CustomSoftDeletes;
    protected $table = 'branches';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'phone',
        'email',
        'latitude',
        'longitude',
        'is_head_office',
        'is_active',
        'metadata',
        'deleted_at',
    ];

    protected $casts = [
        'is_head_office' => 'boolean', 
        'is_active'      => 'boolean', 
        'metadata'       => 'array',   
        'latitude'       => 'decimal:8',
        'longitude'      => 'decimal:8',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];


    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

   
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeadOffice($query)
    {
        return $query->where('is_head_office', true);
    }
}
