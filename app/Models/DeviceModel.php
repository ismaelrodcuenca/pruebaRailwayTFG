<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceModel extends Model
{

    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "device_models";

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
        'name',
        'brand_id'
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
    public function getRecordTitleAttribute()
    {
        return "{$this->brand->name} - {$this->name}";
    }
}
