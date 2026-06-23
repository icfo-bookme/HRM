<?php

namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Branch\Models\Branch;
use App\Traits\CustomSoftDeletes;

class AttendanceDevice extends Model
{
    use HasFactory;
    use CustomSoftDeletes;

    protected $table = 'attendance_devices';

    protected $fillable = [
        'branch_id',
        'device_name',
        'device_code',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'ip_address',
        'port',
        'communication_type',
        'firmware_version',
        'timezone',
        'location',
        'last_sync_at',
        'sync_status',
        'is_active',
        'notes',
        'metadata',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata'  => 'array',
        'last_sync_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the branch that owns this device
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}