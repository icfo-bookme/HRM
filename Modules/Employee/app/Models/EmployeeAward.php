<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAward extends Model
{
    use HasFactory;

    protected $table = 'employee_awards';
    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'award_name',
        'award_date',
        'awarded_by',
        'organization',
        'description',
        'certificate_path',
        'created_at',
    ];

    protected $casts = [
        'award_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeRecent($query, $months = 12)
    {
        return $query->whereDate('award_date', '>=', now()->subMonths($months));
    }
}
