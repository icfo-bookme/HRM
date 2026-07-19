<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeSalaryStructure extends Model
{
    use HasFactory;

    protected $table = 'employee_salary_structure';

    protected $fillable = [
        'employee_id',
        'component_id',
        'amount',
        'effective_from',
        'effective_to',
        'is_percentage',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_percentage' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function component()
    {
        return $this->belongsTo('Modules\Payroll\Models\SalaryComponent', 'component_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

  

    public function scopeActive($query)
    {
        return $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', now());
    }

    public function scopeHistory($query)
    {
        return $query->whereNotNull('effective_to')
                    ->whereDate('effective_to', '<', now());
    }
}
