<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Models\Employee;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $table = 'leave_applications';

    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'applied_at';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'application_no',
        'from_date',
        'to_date',
        'total_days',
        'is_half_day',
        'half_day_period',
        'reason',
        'professional_email',
        'document_path',
        'substitute_employee_id',
        'contact_during_leave',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'from_date'     => 'date:Y-m-d',
        'to_date'       => 'date:Y-m-d',
        'total_days'    => 'decimal:1',
        'is_half_day'   => 'boolean',
        'approved_at'   => 'datetime',
        'applied_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // ===== STATUS CONSTANTS =====
    const STATUS_DRAFT      = 'Draft';
    const STATUS_PENDING    = 'Pending';
    const STATUS_APPROVED   = 'Approved';
    const STATUS_REJECTED   = 'Rejected';
    const STATUS_CANCELLED  = 'Cancelled';
    const STATUS_WITHDRAWN  = 'Withdrawn';

    // ===== RELATIONS =====
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function substitute()
    {
        return $this->belongsTo(Employee::class, 'substitute_employee_id');
    }

    // ===== SCOPES =====
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('from_date', [$from, $to])
            ->orWhereBetween('to_date', [$from, $to]);
    }

    // ===== ACCESSORS =====
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            self::STATUS_DRAFT     => 'gray',
            self::STATUS_PENDING   => 'yellow',
            self::STATUS_APPROVED  => 'green',
            self::STATUS_REJECTED  => 'red',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_WITHDRAWN => 'orange',
        ];

        $color = $colors[$this->status] ?? 'gray';
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800\">{$this->status}</span>";
    }

    // ===== HELPERS =====
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING]);
    }

    /**
     * Generate unique application number
     */
    public static function generateApplicationNo(): string
    {
        $prefix = 'LV';
        $date   = now()->format('Ymd');
        $last   = self::whereDate('applied_at', today())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $last);
    }
}