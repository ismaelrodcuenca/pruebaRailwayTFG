<?php

namespace App\Models;

use DB;
use Filament\Forms\Components\BelongsToSelect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "invoices";

    /**
     * Propiedad protegida que define los atributos que no pueden ser asignados masivamente.
     * 
     * @var array $guarded Atributos protegidos contra asignación masiva.
     */
    protected $guarded = ['id'];

    /**
     * Propiedad protegida que define los atributos que pueden ser asignados masivamente.
     * 
     * @var array $fillable Atributos permitidos para asignación masiva.
     */
    protected $fillable = [
        'invoice_number',
        'base',
        'taxes',
        'total',
        'is_refund',
        'is_down_payment',
        'work_order_id',
        'client_id',
        'store_id',
        'company_id',
        'payment_method_id',
        'user_id',
        'comment'
    ];

    
    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = strtoupper($value);
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (!$invoice->is_refund) {
                $invoice->invoice_number = $invoice->generateInvoiceNumber($invoice->work_order_id);
            }

        });
        static::created(function ($invoice) {
           
            if ($invoice->workOrder) {
                $workOrderItems = $invoice->workOrder->itemWorkOrders;
                //dd($workOrderItems);
               foreach ($workOrderItems as $item) {
                    $invoiceItem = new InvoiceItem();
                    $invoiceItem->invoice_id = $invoice->id;
                    $invoiceItem->item_id = $item->id;
                    $invoiceItem->modified_amount = $item->modified_amount ?? $item->item->price;
                    $invoice->is_refund ?? $invoiceItem->modified_amount = $invoiceItem->modified_amount * -1;
                    $invoiceItem->save();
                }
            }
        });
    }

    public function generateInvoiceNumber(?int $workOrderId): string
    {
        $today = now()->format('Ymd');
        if ($workOrderId) {
            $count = DB::table('invoices')
                ->where('work_order_id', $workOrderId)
                ->where('store_id', $this->store_id)
                ->count() + 1;
            $workOrderNumber = DB::table('work_orders')
                ->where('id', $workOrderId)
                ->value('work_order_number');
            return "W{$workOrderNumber}-{$count}-{$this->store_id}-{$today}";
        } else {
            $count = DB::table('invoices')
                ->whereNull('work_order_id')
                ->where('store_id', $this->store_id)
                ->count() + 1;
            return "S{$count}-{$this->store_id}-{$today}";
        }
    }
    public function invoiceItems(): hasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
