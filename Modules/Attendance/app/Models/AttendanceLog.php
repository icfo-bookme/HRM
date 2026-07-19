<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;

class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';

    protected $fillable = [
        'employee_id',
        'device_id',
        'punch_datetime',
        'punch_type',
        'source',
        'latitude',
        'longitude',
        'ip_address',
        'verification_method',
        'raw_log_id',
        'is_processed',
        'processing_date',
        'remarks',
        'metadata',
    ];

    protected $casts = [
        'punch_datetime' => 'datetime',
        'processing_date' => 'datetime',
        'is_processed' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function device()
    {
        return $this->belongsTo(AttendanceDevice::class, 'device_id');
    }
}