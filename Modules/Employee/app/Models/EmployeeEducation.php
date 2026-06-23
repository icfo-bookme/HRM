<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_education';

    protected $fillable = [
        'employee_id',
        'degree',
        'major_subject',
        'institution',
        'board_university',
        'passing_year',
        'result_type',
        'result_value',
        'duration_from',
        'duration_to',
        'country',
        'certificate_path',
        'is_highest',
        'created_at',
    ];

    protected $casts = [
        'is_highest' => 'boolean',
        'duration_from' => 'date',
        'duration_to' => 'date',
        'created_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeHighest($query)
    {
        return $query->where('is_highest', true);
    }
}
