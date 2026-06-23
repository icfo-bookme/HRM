<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'document_categories';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'requires_expiry',
        'is_mandatory',
        'retention_days',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'requires_expiry' => 'boolean',
        'is_mandatory' => 'boolean',
        'metadata' => 'json',
        'is_active' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }
}
