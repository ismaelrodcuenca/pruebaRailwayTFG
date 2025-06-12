<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Device extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "devices";

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
        'has_no_serial_or_imei', 
        'serial_number', 
        'IMEI', 
        'colour', 
        'unlock_code', 
        'device_model_id', 
        'client_id'];

    /**
     * Propiedad protegida que define los atributos que deben ser convertidos a tipos específicos.
     * 
     * @var array $casts Atributos con conversiones de tipo.
     */
    protected $casts = ['has_no_serial_or_imei' => 'boolean'];


    public function setColourAttribute($value)
    {
        $this->attributes['colour'] = strtoupper($value);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'device_model_id');
    }
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}
