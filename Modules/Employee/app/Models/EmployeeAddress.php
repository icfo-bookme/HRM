<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAddress extends Model
{
    use HasFactory;

    protected $table = 'employee_addresses';

    protected $fillable = [
        'employee_id',
        'address_type',
        'house_no',
        'road_no',
        'road_name',
        'village',
        'area',
        'post_office',
        'postal_code',

        'city',
        'upazila',
        'district',
        'division',

        'state',
        'country',

        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('address_type', $type);
    }
}