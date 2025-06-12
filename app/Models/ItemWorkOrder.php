<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemWorkOrder extends Pivot
{
    public $incrementing = true;

    protected $table = 'item_work_order';

    protected $primaryKey = 'id';

    protected $fillable = [
        'modified_amount',
        'work_order_id',
        'item_id',
    ];
    
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

}
