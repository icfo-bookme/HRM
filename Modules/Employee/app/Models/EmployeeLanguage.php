<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeLanguage extends Model
{
    use HasFactory;

    protected $table = 'employee_languages';

    protected $fillable = [
        'employee_id',
        'language_name',
        'proficiency',
        'can_read',
        'can_write',
        'can_speak',
        'created_at',
    ];

    protected $casts = [
        'can_read' => 'boolean',
        'can_write' => 'boolean',
        'can_speak' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language_name', $language);
    }

    public function scopeByProficiency($query, $proficiency)
    {
        return $query->where('proficiency', $proficiency);
    }
}
