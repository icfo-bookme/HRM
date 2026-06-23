<?php

namespace Modules\Department\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Branch\Models\Branch;
use App\Models\User;
use App\Traits\CustomSoftDeletes;

class Department extends Model
{
    use HasFactory;
    use CustomSoftDeletes;
  
   protected $table = 'departments';

    protected $fillable = [
        'branch_id',
        'cost_center_id',
        'parent_id',
        'code',
        'name',
        'description',
        'head_employee_id',
        'email',
        'phone',
        'is_active',
        'sort_order',
        'metadata',
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata'  => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the branch that owns this department
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    /**
     * Get the parent department
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id', 'id');
    }

    /**
     * Get child departments
     */
    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id', 'id');
    }

    /**
     * Get the head employee
     */
    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_employee_id', 'id');
    }
}
