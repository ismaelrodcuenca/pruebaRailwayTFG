<?php

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Item extends Model
{
    protected $table = "items";
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'cost',
        'price',
        'distributor',
        'type_id',
        'category_id'
    ];
    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {

            $components = request()->input('components', []);
            //dd($components);
            $formData = [];
            if (!empty($components) && isset($components[0]['snapshot'])) {
                try {
                    $snapshot = json_decode($components[0]['snapshot'], true);
                  
                    $formData = Arr::get($snapshot, 'data.data.0', []);
                } catch (\Throwable $e) {
                   
                    $formData = [];
                }
            }
            $linkToStores = Arr::get($formData, 'link_to_stores', false);
            $linkToDevice = Arr::get($formData, 'link_item_device_model', false);
            $deviceModelId = Arr::get($formData, 'device_model_id', null);
            //dd( $linkToDevice, $deviceModelId);
            if ($linkToStores) {
                $allStores = Store::all();
                foreach ($allStores as $store) {
                    $item->stores()->attach($store->id, ['quantity' => 0]);
                }
            }
            if ($linkToDevice && $deviceModelId) {
                $item->deviceModels()->syncWithoutDetaching([$deviceModelId]);
            }
        });

    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function setDistributorAttribute($value)
    {
        $this->attributes['distributor'] = strtoupper($value);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tax(): HasOneThrough
    {
        return $this->hasOneThrough(Tax::class, Category::class);
    }

    public function deviceModels(): BelongsToMany
    {
        return $this->belongsToMany(DeviceModel::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'stocks')->withPivot('quantity');
    }

    //NO SE VA A USAR. 
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(ItemWorkOrder::class);
    }
}
