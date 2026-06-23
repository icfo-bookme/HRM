<?php

namespace Modules\Salary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Models\Employee;

class EmployeeSalaryStructure extends Model
{
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
        'amount'         => 'decimal:4',
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'is_percentage'  => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Get the employee that owns this salary structure entry.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * Get the salary component associated with this entry.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(SalaryComponent::class, 'component_id', 'id');
    }
}