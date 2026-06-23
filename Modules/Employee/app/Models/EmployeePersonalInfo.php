<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeePersonalInfo extends Model
{
    use HasFactory;

    protected $table = 'employee_personal_info';
    protected $primaryKey = 'employee_id';

    public $incrementing = false;
    public $timestamps = false; // because you already use created_at & updated_at manually

    protected $fillable = [
        'employee_id',

        'first_name',
        'last_name',
        'full_name',
        'display_name',

        'phone',
        'phone_2',
        'email',

        'profile_photo',
        'signature_file',

        'gender',
        'date_of_birth',
        'nationality',

        'marital_status',
        'blood_group',

        'religion',

        'father_name',
        'mother_name',
        'spouse_name',

        'personal_email',
        'personal_mobile',

        'place_of_birth',
        'metadata',

        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'metadata' => 'json',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Employee relation
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}