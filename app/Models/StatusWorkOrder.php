<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StatusWorkOrder extends Pivot
{
    public $incrementing = true;

    protected $table = 'status_work_order';

    public $timestamps = true;

    protected $fillable = [
        'status_id',
        'work_order_id',
        'user_id',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
