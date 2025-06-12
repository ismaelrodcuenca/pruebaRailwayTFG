<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "brands";

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
    protected $fillable = ['name'];

    public function device_models(): HasMany
    {
        return $this->hasMany(DeviceModel::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
