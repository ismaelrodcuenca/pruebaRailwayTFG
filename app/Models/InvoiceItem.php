<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoiceItem extends Pivot
{
    public $timestamps = true;
    public $incrementing = true;
    protected $table = 'invoice_item';

    protected $fillable = [
        'modified_amount',
        'invoice_id',
        'item_id',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
