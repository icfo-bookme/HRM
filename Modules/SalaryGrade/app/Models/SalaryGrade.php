<?php

namespace Modules\SalaryGrade\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryGrade extends Model
{
    use HasFactory;

    protected $table = 'salary_grades';

    protected $fillable = [
        'name',
        'min_salary',
        'max_salary',
        'currency',
        'is_active',
        'metadata',
        'deduct_late_for_payroll',
        'pay_overtime_for_payroll',
        'late_deduction_per_minute',
        'half_day_deduction_percent',
        'absent_deduction_days',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active'  => 'boolean',
        'metadata'   => 'array', 
        'deduct_late_for_payroll' => 'boolean',
        'pay_overtime_for_payroll' => 'boolean',
        'late_deduction_per_minute' => 'decimal:4',
        'half_day_deduction_percent' => 'decimal:2',
        'absent_deduction_days' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}