<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeExperience extends Model
{
    use HasFactory;

    protected $table = 'employee_experience';

    protected $fillable = [
        'employee_id',
        'company_name',
        'designation',
        'department',
        'from_date',
        'to_date',
        'is_current',
        'responsibilities',
        'achievements',
        'reason_for_leaving',
        'salary_scale',
        'reference_name',
        'reference_phone',
        'reference_email',
        'certificate_path',
        'created_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'is_current' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopePrevious($query)
    {
        return $query->where('is_current', false);
    }
}
