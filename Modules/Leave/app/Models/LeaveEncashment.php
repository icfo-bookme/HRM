<?php

namespace Modules\Leave\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\Models\Employee;

class LeaveEncashment extends Model
{
    use HasFactory;

    protected $table = 'leave_encashment';

    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'encashment_date',
        'days_encashed',
        'amount_per_day',
        'total_amount',
        'payroll_run_id',
        'reason',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected $casts = [
        'encashment_date' => 'date:Y-m-d',
        'days_encashed'   => 'decimal:1',
        'amount_per_day'  => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'approved_at'     => 'datetime',
        'created_at'      => 'datetime',
    ];

    // ===== STATUS CONSTANTS =====
    const STATUS_PENDING  = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_PAID     = 'Paid';

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

    // ===== SCOPES =====
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // ===== ACCESSORS =====
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING  => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_PAID     => 'blue',
        ];

        $color = $colors[$this->status] ?? 'gray';
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800\">{$this->status}</span>";
    }

    // ===== HELPERS =====
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Calculate total amount if amount_per_day and days_encashed are set
     */
    public function calculateTotalAmount(): ?float
    {
        if ($this->amount_per_day && $this->days_encashed) {
            return round($this->amount_per_day * $this->days_encashed, 2);
        }
        return null;
    }
}