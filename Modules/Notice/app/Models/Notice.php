<?php

namespace Modules\Notice\Models;

use App\Traits\CustomSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notice extends Model
{
    use HasFactory;
    use CustomSoftDeletes;

    protected $table = 'notices';

    protected $fillable = [
        'branch_id',
        'notice_no',
        'title',
        'slug',
        'description',
        'notice_type',
        'priority',
        'publish_date',
        'expiry_date',
        'target_type',
        'attachment_path',
        'is_popup',
        'is_pinned',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_at',
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expiry_date'  => 'datetime',
        'is_popup'     => 'boolean',
        'is_pinned'    => 'boolean',
        'is_active'    => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notice) {
            if (empty($notice->notice_no)) {
                $notice->notice_no = 'NTC-' . strtoupper(Str::random(8));
            }
            if (empty($notice->slug)) {
                $notice->slug = Str::slug($notice->title) . '-' . Str::random(6);
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Models\Branch::class);
    }

    public function acknowledgements()
    {
        return $this->hasMany(NoticeAcknowledgement::class);
    }

    public function views()
    {
        return $this->hasMany(NoticeView::class);
    }
}