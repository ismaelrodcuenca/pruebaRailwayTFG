<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;


class WorkOrder extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "work_orders";

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
        'work_order_number',
        'work_order_number_warranty',
        'failure',
        'private_comment',
        'comment',
        'physical_condition',
        'humidity',
        'test',
        'user_id',
        'device_id',
        'repair_time_id',
        'store_id',
        'closure_id',
    ];
    
    public function setFailureAttribute($value)
    {
        $this->attributes['failure'] = strtoupper($value);
    }

    public function setPrivateCommentAttribute($value)
    {
        $this->attributes['private_comment'] = strtoupper($value);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = strtoupper($value);
    }

    public function setHumidityAttribute($value)
    {
        $this->attributes['humidity'] = strtoupper($value);
    }

    public function setTestAttribute($value)
    {
        $this->attributes['test'] = strtoupper($value);
    }

    protected static function booted()
    {
        static::creating(function ($workOrder) {
            DB::transaction(function () use ($workOrder) {
            $store = Store::lockForUpdate()->find($workOrder->store_id);

            if (!$store) {
                throw new \Exception("Tienda no encontrada. \n Tienda Pedido: ". $workOrder->store_id. "\n Tienda Sesion: ". session('store_id'));
            }
            $workOrder->work_order_number = $store->work_order_number;
            });
        });

        static::created(function ($workOrder) {
            DB::table('status_work_order')->insert([
            'work_order_id' => $workOrder->id,
            'status_id' => Status::where('name', 'pendiente')->value('id'),
            'user_id' => $workOrder->user_id,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        });
    }


    public function itemWorkOrders(): hasMany
    {
        return $this->hasMany(ItemWorkOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function closure(): HasOne
    {
        return $this->hasOne(Closure::class);
    }

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function repairTime(): BelongsTo
    {
        return $this->belongsTo(RepairTime::class);
    }

    public function statusWorkOrders(): HasMany
    {
        return $this->hasMany(StatusWorkOrder::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }


}
