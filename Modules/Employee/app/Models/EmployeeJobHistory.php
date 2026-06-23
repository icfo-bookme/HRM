<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeJobHistory extends Model
{
    use HasFactory;

    protected $table = 'employee_job_history';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'effective_date',
        'change_type',
        'from_branch_id',
        'to_branch_id',
        'from_dept_id',
        'to_dept_id',
        'from_desig_id',
        'to_desig_id',
        'from_grade_id',
        'to_grade_id',
        'from_salary',
        'to_salary',
        'reason',
        'remarks',
        'approved_by',
        'document_ref',
        'created_at',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'from_salary' => 'decimal:2',
        'to_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function fromBranch()
    {
        return $this->belongsTo('Modules\Branch\Models\Branch', 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo('Modules\Branch\Models\Branch', 'to_branch_id');
    }

    public function fromDepartment()
    {
        return $this->belongsTo('Modules\Department\Models\Department', 'from_dept_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo('Modules\Department\Models\Department', 'to_dept_id');
    }

    public function fromDesignation()
    {
        return $this->belongsTo('Modules\Designation\Models\Designation', 'from_desig_id');
    }

    public function toDesignation()
    {
        return $this->belongsTo('Modules\Designation\Models\Designation', 'to_desig_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function scopeByChangeType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->whereDate('effective_date', '>=', now()->subDays($days));
    }
}
