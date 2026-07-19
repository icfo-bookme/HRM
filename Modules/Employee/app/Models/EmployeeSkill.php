<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSkill extends Model
{
    use HasFactory;

    protected $table = 'employee_skills';

    protected $fillable = [
        'employee_id',
        'category_id',
        'skill_name',
        'description',
        'proficiency',
        'years_of_experience',
        'last_used_date',
        'certification',
        'is_active',
    ];

    protected $casts = [
        'years_of_experience' => 'decimal:1',
        'last_used_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function category()
    {
        return $this->belongsTo(SkillCategory::class, 'category_id');
    }

    public function scopeByProficiency($query, $proficiency)
    {
        return $query->where('proficiency', $proficiency);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
