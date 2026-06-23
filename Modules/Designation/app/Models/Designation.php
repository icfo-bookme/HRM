<?php

namespace Modules\Designation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Company\Models\Company;
use Modules\Department\Models\Department;
use Modules\SalaryGrade\Models\SalaryGrade;

class Designation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'designations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
        'department_id',
        'grade_id',
        
        'title',
        'level',
        'responsibilities',
        'requirements',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'responsibilities' => 'array',
        'requirements'     => 'array', 
        'is_active'        => 'boolean',
        'level'            => 'integer',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

 

    public function department(): BelongsTo
    {
        
        return $this->belongsTo(Department::class, 'department_id');
    }


    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'grade_id');
    }

 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}