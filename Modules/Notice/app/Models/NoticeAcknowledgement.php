<?php

namespace Modules\Notice\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;

class NoticeAcknowledgement extends Model
{
    protected $table = 'notice_acknowledgements';

    public $timestamps = false;

    protected $fillable = [
        'notice_id',
        'employee_id',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}