<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDependent extends Model
{
    use HasFactory;

    protected $table = 'employee_dependents';

    protected $fillable = [
        'employee_id',
        'full_name',
        'relation',
        'date_of_birth',
        'nid_number',
        'phone',
        'email',
        'occupation',
        'is_nominee',
        'nominee_percent',
        'priority_order',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_nominee' => 'boolean',
        'nominee_percent' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeNominees($query)
    {
        return $query->where('is_nominee', true)->orderBy('priority_order');
    }

    public function scopeByRelation($query, $relation)
    {
        return $query->where('relation', $relation);
    }
}
