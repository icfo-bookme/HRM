<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id',
        'category_id',
        'document_name',
        'file_path',
        'file_hash',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'document_number',
        'issuing_authority',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function category()
    {
        return $this->belongsTo('Modules\Employee\Models\DocumentCategory', 'category_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Employee::class, 'verified_by');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeExpired($query)
    {
        return $query->whereDate('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        $expiryDate = now()->addDays($days)->toDateString();
        return $query->whereDate('expiry_date', '<=', $expiryDate)
                    ->whereDate('expiry_date', '>', now());
    }
}
