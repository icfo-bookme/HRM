<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBanking extends Model
{
    use HasFactory;

    protected $table = 'employee_banking';

    protected $fillable = [
        'employee_id',
        'bank_name',
        'bank_branch',
        'bank_account',
        'bank_routing',
        'iban',
        'swift_code',
        'mfs_type',
        'mfs_number',
        'payment_method',
        'is_primary',
        'verification_status',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Employee::class, 'verified_by');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'Verified');
    }
}
